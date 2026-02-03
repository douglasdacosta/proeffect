# Análise: Congelamento de Prazo de Entrega e Alerta Dias

**Data**: 01/02/2026  
**Autor**: Análise Técnica  
**Objetivo**: Persistir valores calculados de prazo de entrega e alerta dias ao mudar status para Expedição ou Entregue, usando campos dedicados para congelamento

---

## 1. Comportamento Atual

### 1.1 Cálculo Dinâmico

Atualmente, os campos `data_prazo_entrega` e `alerta_dias` são calculados dinamicamente a cada requisição no método `index()` do `ProjetosController`:

**Localização**: `ProjetosController.php` linhas 195-250

**Lógica Atual**:

```php
// Para status "EM PROGRAMAÇÃO" (id = 4)
if($status_projetos_id == 4) {
    // Calcula prazo baseado no tempo do projeto
    $data_historico = new DateTime($projeto->data_historico);
    $data_prazo_entrega = clone $data_historico;
    $data_prazo_entrega = Carbon::parse($data_prazo_entrega);
    $data_prazo_entrega->addWeekdays($prazo_entrega);
    
    // Calcula diferença em dias
    $hoje = Carbon::today();
    $diferenca = Carbon::parse($data_prazo_entrega)->diffInDays($hoje, false);
    $projeto->cor_alerta = 'green';
    
    if($diferenca > 0) {
        $diferenca = $diferenca * -1;
        $projeto->cor_alerta = 'red';
    }
    
    $projeto->alerta_dias = $diferenca;
}

// Para status "EM AVALIAÇÃO" (id = 3)
if($status_projetos_id == 3) {
    // Lógica similar para calcular prazo
}
```

### 1.2 Problemas Identificados

1. **Performance**: Cálculo executado a cada listagem de projetos
2. **Inconsistência**: Valores mudam conforme o tempo passa
3. **Histórico**: Não há registro do prazo real quando o projeto foi finalizado
4. **Relatórios**: Impossível gerar relatórios precisos de prazos cumpridos

---

## 2. Requisito da Mudança

### 2.1 Objetivo

**Congelar** os valores de `data_prazo_entrega` e `alerta_dias` quando o projeto atingir determinados status, salvando-os nos campos `data_entrega_congelada` e `alerta_dias_congelado`.

### 2.2 Gatilhos para Congelamento

Os valores devem ser salvos no banco quando o status mudar para:

- **Expedição** (status_projetos_id = 4, quando etapa_projeto_id = 5)
- **Entregue** (sub_status_projetos_codigo = 36)

### 2.3 Comportamento Esperado

#### Na Alteração de Status:
1. Sistema detecta mudança para status de "congelamento"
2. Calcula `data_prazo_entrega` e `alerta_dias`
3. Salva valores nas colunas `data_entrega_congelada` e `alerta_dias_congelado` da tabela `projetos`
4. Valores ficam fixos mesmo com passagem do tempo

#### Na Exibição:
1. Se `data_entrega_congelada` existe no banco → mostra valor salvo
2. Se `data_entrega_congelada` é NULL → calcula dinamicamente (comportamento atual)
3. Se `alerta_dias_congelado` existe no banco → mostra valor salvo
4. Se `alerta_dias_congelado` é NULL → calcula dinamicamente (comportamento atual)

---

## 3. Análise da Estrutura Atual

### 3.1 Tabela `projetos`

**Migration**: `database/migrations/2025_09_26_000005_projetos.php`

**Colunas Existentes**:
- ✅ `data_entrega` (DATETIME) - já existe, **não será utilizada** para congelamento
- ❌ `data_entrega_congelada` - **NÃO existe**, precisa ser criada
- ❌ `alerta_dias_congelado` - **NÃO existe**, precisa ser criada

**Colunas que precisam ser criadas**:
```php
$table->dateTime('data_entrega_congelada')->nullable();
$table->integer('alerta_dias_congelado')->nullable();
```

### 3.2 Controller - Método `salva()`

**Localização**: `ProjetosController.php` linhas 653-768

**Lógica atual**:
- Detecta mudança de status (linha 669)
- Cria histórico quando muda status
- **NÃO** calcula/salva prazo de entrega

### 3.3 Controller - Método `index()`

**Localização**: `ProjetosController.php` linhas 195-250

**Lógica atual**:
- Calcula `data_prazo_entrega` dinamicamente
- Calcula `alerta_dias` dinamicamente
- Armazena em memória (não persiste)

### 3.4 View

**Localização**: `resources/views/projetos.blade.php`

**Exibição atual**:
- Mostra valores do array `$dados['departamentos']`
- Valores são sempre calculados dinamicamente

---

## 4. Impacto das Mudanças

### 4.1 Banco de Dados

#### Nova Migration Necessária

```php
// 2026_02_01_000001_add_congelamento_prazo_to_projetos.php
Schema::table('projetos', function (Blueprint $table) {
    $table->dateTime('data_entrega_congelada')->nullable()->after('data_entrega');
    $table->integer('alerta_dias_congelado')->nullable()->after('data_entrega_congelada');
});
```

#### Colunas Afetadas
- `data_entrega` - já existe, **não será usada** para congelamento
- `data_entrega_congelada` - nova coluna, será preenchida
- `alerta_dias_congelado` - nova coluna, será preenchida

### 4.2 Controller - Alterações Necessárias

#### 4.2.1 Método `salva()`

**Modificações**:

1. Detectar mudança para status de "congelamento"
2. Calcular prazo de entrega
3. Calcular alerta dias
4. Salvar no banco

**Código a adicionar** (após linha 717):

```php
// Verificar se deve "congelar" os valores de prazo
$deveCongelarPrazo = false;

// Caso 1: Mudou para Expedição (etapa 5 e status específico)
if ($etapa_projeto_id == 5 && $status_id == 36) {
    $deveCongelarPrazo = true;
}

// Caso 2: Status EM PROGRAMAÇÃO (id = 4)
if ($status_projetos_id == 4) {
    $deveCongelarPrazo = true;
}

// Se deve congelar e ainda não tem valores salvos
if ($deveCongelarPrazo && empty($projeto->data_entrega_congelada)) {
    // Buscar configurações
    $configuracaoProjetos = ConfiguracoesProjetos::where('id', 1)->first();
    $configuracaoProjetos = json_decode($configuracaoProjetos->dados, true);
    
    // Buscar histórico mais recente
    $HistoricosEtapasProjetos = HistoricosEtapasProjetos::where('projetos_id', $projeto->id)
        ->orderBy('created_at', 'DESC')
        ->first();
    
    if ($HistoricosEtapasProjetos) {
        $data_historico = new DateTime($HistoricosEtapasProjetos->created_at);
        $prazo_entrega = 0;
        
        // Calcular prazo baseado no tempo do projeto
        if (!empty($projeto->tempo_projetos)) {
            $t = explode(':', $projeto->tempo_projetos);
            $horas = (int)$t[0];
            $minutos = isset($t[1]) ? (int)$t[1] : 0;
            $segundos = isset($t[2]) ? (int)$t[2] : 0;
            $tempo_projeto = number_format($horas + ($minutos / 60) + ($segundos / 3600), 2);
            
            if ($tempo_projeto <= 2 && !empty($configuracaoProjetos['0_2_horas'])) {
                $prazo_entrega = $configuracaoProjetos['0_2_horas'];
            } elseif ($tempo_projeto > 2 && $tempo_projeto <= 6 && !empty($configuracaoProjetos['2_6_horas'])) {
                $prazo_entrega = $configuracaoProjetos['2_6_horas'];
            } elseif ($tempo_projeto > 6 && $tempo_projeto <= 10 && !empty($configuracaoProjetos['6_10_horas'])) {
                $prazo_entrega = $configuracaoProjetos['6_10_horas'];
            } elseif ($tempo_projeto > 10 && !empty($configuracaoProjetos['10_ou_mais_horas'])) {
                $prazo_entrega = $configuracaoProjetos['10_ou_mais_horas'];
            }
        }
        
        if ($prazo_entrega > 0) {
            // Calcular data de entrega
            $data_prazo_entrega = Carbon::parse($data_historico);
            $data_prazo_entrega->addWeekdays($prazo_entrega);
            
            // Calcular alerta dias
            $hoje = Carbon::today();
            $diferenca = $data_prazo_entrega->diffInDays($hoje, false);
            
            if ($diferenca > 0) {
                $diferenca = $diferenca * -1;
            }
            
            // Salvar valores congelados
            $projeto->data_entrega_congelada = $data_prazo_entrega->format('Y-m-d H:i:s');
            $projeto->alerta_dias_congelado = $diferenca;
        }
    }
}
```

#### 4.2.2 Método `index()`

**Modificações**:

1. Verificar se valores já existem no banco
2. Se existirem, usar valores salvos
3. Se não existirem, calcular dinamicamente (manter lógica atual)

**Código a modificar** (linhas 195-250):

```php
$prazo_entrega = '';

// NOVO: Verificar se valores já estão salvos no banco
if (!empty($projeto->data_entrega_congelada) && isset($projeto->alerta_dias_congelado)) {
    // Usar valores congelados do banco
    $projeto->data_prazo_entrega = (new DateTime($projeto->data_entrega_congelada))->format('d/m/Y');
    $projeto->alerta_dias = $projeto->alerta_dias_congelado;
    $projeto->cor_alerta = $projeto->alerta_dias < 0 ? 'red' : 'green';
    
} else {
    // LÓGICA ATUAL: Calcular dinamicamente
    if (!empty($projeto->tempo_projetos)) {
        // ... código atual de cálculo ...
    }
}
```

### 4.3 View - Alterações Necessárias

**Nenhuma alteração necessária** - a view já recebe os valores corretos através do array `$dados`.

---

## 5. Plano de Implementação

### 5.1 Fase 1: Preparação do Banco

**Tarefa 1.1**: Criar migration para campos `data_entrega_congelada` e `alerta_dias_congelado`
- Arquivo: `database/migrations/2026_02_01_000001_add_congelamento_prazo_to_projetos.php`
- Comando: `php artisan make:migration add_congelamento_prazo_to_projetos`

**Tarefa 1.2**: Executar migration
- Comando: `php artisan migrate`

### 5.2 Fase 2: Modificar Controller

**Tarefa 2.1**: Criar método auxiliar `calcularECongelarPrazo()`
- Extrair lógica de cálculo para método reutilizável
- Facilitar manutenção

**Tarefa 2.2**: Modificar método `salva()`
- Detectar mudança para status de congelamento
- Chamar método de cálculo
- Salvar valores no banco

**Tarefa 2.3**: Modificar método `index()`
- Priorizar valores salvos no banco
- Fallback para cálculo dinâmico se valores não existirem

### 5.3 Fase 3: Testes

**Teste 3.1**: Criar novo projeto
- Verificar que prazo NÃO é calculado inicialmente
- Verificar que campos ficam NULL

**Teste 3.2**: Mudar para status "EM PROGRAMAÇÃO"
- Verificar que `data_entrega_congelada` é preenchida
- Verificar que `alerta_dias_congelado` é preenchido
- Verificar que valores permanecem fixos após alguns dias

**Teste 3.3**: Mudar para status "ENTREGUE"
- Verificar que valores continuam congelados
- Verificar que não são recalculados

**Teste 3.4**: Projetos antigos (sem valores congelados)
- Verificar que ainda calculam dinamicamente
- Verificar que exibição funciona corretamente

### 5.4 Fase 4: Migração de Dados (Opcional)

**Cenário**: Projetos existentes que já estão em Expedição/Entregue mas não têm valores congelados

**Opções**:

1. **Não fazer nada**: Manter cálculo dinâmico para projetos antigos
2. **Script de migração**: Calcular e salvar valores para projetos existentes

**Recomendação**: Opção 1 (não fazer nada) - mais simples e seguro

---

## 6. Riscos e Considerações

### 6.1 Riscos

| Risco | Impacto | Probabilidade | Mitigação |
|-------|---------|---------------|-----------|
| Valores congelados incorretos | Alto | Baixa | Testes extensivos antes do deploy |
| Performance degradada | Médio | Baixa | Calcular apenas quando necessário |
| Inconsistência entre projetos antigos/novos | Baixo | Alta | Documentar comportamento híbrido |

### 6.2 Considerações Importantes

1. **Recálculo**: Uma vez congelado, não será possível recalcular automaticamente
   - Solução: Adicionar botão "Recalcular Prazo" (futuro)

2. **Mudança Manual**: Se usuário alterar `tempo_projetos` depois do congelamento
   - Valores congelados não mudam automaticamente
   - Comportamento esperado e correto

3. **Backwards Compatibility**: Projetos antigos continuam funcionando
   - Cálculo dinâmico para projetos sem valores congelados

---

## 7. Estimativa de Esforço

| Tarefa | Esforço Estimado | Complexidade |
|--------|------------------|--------------|
| Criar migration | 15 minutos | Baixa |
| Modificar método `salva()` | 2 horas | Média |
| Modificar método `index()` | 1 hora | Baixa |
| Criar método auxiliar | 1 hora | Média |
| Testes manuais | 2 horas | Média |
| Documentação | 1 hora | Baixa |
| **TOTAL** | **~7-8 horas** | **Média** |

---

## 8. Exemplo de Fluxo

### 8.1 Cenário: Novo Projeto

```
1. Criar projeto → status "SOLICITADO"
    - data_entrega_congelada: NULL
    - alerta_dias_congelado: NULL
   - Exibição: Calcula dinamicamente

2. Mudar para "EM AVALIAÇÃO"
    - data_entrega_congelada: NULL (ainda não congela)
    - alerta_dias_congelado: NULL
   - Exibição: Calcula dinamicamente

3. Mudar para "EM PROGRAMAÇÃO" (etapa 4)
    - data_entrega_congelada: 2026-02-15 (CONGELADO)
    - alerta_dias_congelado: 5 (CONGELADO)
   - Exibição: Mostra valores salvos

4. Passam-se 3 dias...
    - data_entrega_congelada: 2026-02-15 (NÃO MUDA)
    - alerta_dias_congelado: 5 (NÃO MUDA)
   - Exibição: Mostra valores salvos (histórico real)

5. Mudar para "ENTREGUE"
    - data_entrega_congelada: 2026-02-15 (mantém)
    - alerta_dias_congelado: 5 (mantém)
   - Exibição: Mostra valores salvos
```

---

## 9. Conclusão

Esta mudança traz os seguintes benefícios:

### ✅ Vantagens
- Preserva histórico real de prazos
- Melhora performance da listagem
- Permite relatórios precisos
- Mantém compatibilidade com projetos antigos

### ⚠️ Atenção
- Valores congelados não mudam automaticamente
- Necessário testar cenários de mudança de status
- Migration obrigatória antes do deploy

### 📋 Próximos Passos
1. Criar migration
2. Implementar mudanças no controller
3. Testar em ambiente de desenvolvimento
4. Deploy em homologação
5. Validação com usuários
6. Deploy em produção

---

## 10. Referências

- **Arquivo**: `app/Http/Controllers/ProjetosController.php`
- **Método Principal**: `salva()` (linhas 653-768)
- **Método de Listagem**: `index()` (linhas 44-333)
- **Migration**: `database/migrations/2025_09_26_000005_projetos.php`
- **Configurações**: Tabela `configuracoes_projetos`
