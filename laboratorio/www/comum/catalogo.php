<?php
// Informações de vitrine (imagem, descrição, avaliações). Só servem para o visual:
// preço e estoque continuam vindo do banco de dados.

const CATALOGO = [
    1 => [
        'img' => 'notebook', 'categoria' => 'Notebooks', 'marca' => 'Lab Tech', 'nota' => 4.7, 'avaliacoes' => 312, 'desconto' => 15,
        'resumo' => 'Tela 14" Full HD, 16 GB de RAM e SSD de 512 GB.',
        'itens' => ['Processador de 8 núcleos', 'Memória RAM de 16 GB', 'SSD NVMe de 512 GB', 'Tela IPS de 14" Full HD', 'Bateria para até 10 horas', 'Peso de 1,3 kg'],
    ],
    2 => [
        'img' => 'smartphone', 'categoria' => 'Celulares', 'marca' => 'Lab Mobile', 'nota' => 4.6, 'avaliacoes' => 541, 'desconto' => 12,
        'resumo' => 'Tela 6,5" AMOLED, 256 GB e câmera tripla de 50 MP.',
        'itens' => ['Tela AMOLED de 6,5" a 120 Hz', 'Armazenamento de 256 GB', 'Câmera tripla de 50 MP', 'Bateria de 5000 mAh', 'Carregamento rápido de 45 W'],
    ],
    3 => [
        'img' => 'fone', 'categoria' => 'Áudio', 'marca' => 'Lab Sound', 'nota' => 4.5, 'avaliacoes' => 1204, 'desconto' => 20,
        'resumo' => 'Cancelamento de ruído e até 30 horas de bateria.',
        'itens' => ['Cancelamento ativo de ruído', 'Bluetooth 5.3', 'Até 30 horas de bateria', 'Microfone para chamadas', 'Dobrável, com estojo'],
    ],
    4 => [
        'img' => 'mouse', 'categoria' => 'Acessórios', 'marca' => 'Lab Tech', 'nota' => 4.8, 'avaliacoes' => 2087, 'desconto' => 10,
        'resumo' => 'Sensor de 4000 DPI, silencioso e com pilha inclusa.',
        'itens' => ['Sensor óptico de até 4000 DPI', 'Cliques silenciosos', 'Conexão sem fio 2,4 GHz', 'Pilha AA inclusa', 'Uso por até 18 meses'],
    ],
];

function info_produto(int $id): array
{
    return CATALOGO[$id] ?? ['img' => 'mouse', 'categoria' => 'Outros', 'marca' => 'Loja Lab', 'nota' => 4.5,
        'avaliacoes' => 0, 'desconto' => 0, 'resumo' => '', 'itens' => []];
}

function img_produto(int $id): string
{
    return '../comum/img/' . info_produto($id)['img'] . '.svg';
}

// Preço "de" (riscado), só para o visual da vitrine.
function preco_de($preco, int $id): float
{
    $d = info_produto($id)['desconto'];
    return $d > 0 ? round((float) $preco / (1 - $d / 100), 2) : (float) $preco;
}

function parcelas($preco): string
{
    $preco = (float) $preco;
    $n = $preco >= 1000 ? 10 : ($preco >= 200 ? 5 : ($preco >= 50 ? 2 : 1));
    return $n === 1 ? 'à vista' : "em até {$n}x de " . moeda($preco / $n) . ' sem juros';
}

function estrelas(float $nota, int $avaliacoes): string
{
    $cheias = (int) round($nota);
    return '<span class="estrelas" aria-label="Nota ' . $nota . ' de 5">'
        . str_repeat('★', $cheias) . str_repeat('☆', 5 - $cheias)
        . '<small>' . number_format($nota, 1, ',', '') . ' (' . number_format($avaliacoes, 0, ',', '.') . ')</small></span>';
}

// Ícones simples (traço), usados no cabeçalho e nas listas.
function icone(string $nome): string
{
    $p = [
        'busca'    => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'usuario'  => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>',
        'carrinho' => '<circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M2 3h3l2.7 12.4a2 2 0 0 0 2 1.6h7.6a2 2 0 0 0 2-1.5L21 7H6"/>',
        'caminhao' => '<path d="M1 4h13v12H1zM14 8h4l4 4v4h-8z"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="18" r="2"/>',
        'escudo'   => '<path d="M12 2 4 5v6c0 5 3.4 9.4 8 11 4.6-1.6 8-6 8-11V5z"/><path d="m9 12 2 2 4-4"/>',
        'troca'    => '<path d="M3 12a9 9 0 0 1 15-6.7L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-15 6.7L3 16"/><path d="M3 21v-5h5"/>',
        'cartao'   => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>',
        'pacote'   => '<path d="M21 8 12 3 3 8v8l9 5 9-5z"/><path d="m3 8 9 5 9-5M12 13v8"/>',
        'perfil'   => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>',
        'painel'   => '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>',
        'sair'     => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5M21 12H9"/>',
        'check'    => '<path d="m5 12 5 5L20 7"/>',
        'cadeado'  => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
    ];
    return '<svg class="icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
        . ($p[$nome] ?? '') . '</svg>';
}
