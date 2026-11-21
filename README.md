# sped-nfe

Biblioteca PHP para gerar, assinar, validar e transmitir **NF-e (modelo 55)** e **NFC-e (modelo 65)** às SEFAZ autorizadoras, com eventos, consultas e distribuição de DF-e.

Este repositório é um **fork** de [nfephp-org/sped-nfe](https://github.com/nfephp-org/sped-nfe), mantido em dia com o original e usado pela API Fiscal do ValuorERP. Correções feitas aqui são enviadas ao projeto original sempre que fazem sentido para todos.

[![PHP](https://img.shields.io/packagist/php-v/nfephp-org/sped-nfe)](https://packagist.org/packages/nfephp-org/sped-nfe)
[![Licença](https://poser.pugx.org/nfephp-org/sped-nfe/license.svg)](LICENSE.md)

## Sumário

- [O que este fork tem a mais](#o-que-este-fork-tem-a-mais)
- [Situação das Notas Técnicas](#situação-das-notas-técnicas)
- [Instalação](#instalação)
- [Requisitos](#requisitos)
- [Uso básico](#uso-básico)
- [Estados atendidos](#estados-atendidos)
- [Documentação](#documentação)
- [Contribuindo](#contribuindo)
- [Créditos e licença](#créditos-e-licença)

## O que este fork tem a mais

Correções e atualizações feitas aqui antes de chegarem ao projeto original (detalhes no `git log`):

- **Pacote de schemas PL_010h v1.03** (13/11/2026: NT 2026.005, 2026.006 e 2026.008) em `schemes/PL_010h`.
- **NT 2026.005 (Simples Nacional na Reforma):**
  - `cAtivSN` na posição exigida pelo XSD;
  - total `gTribSN` só quando algum item tem o grupo (antes saía zerado em toda nota com IBS/CBS, e a SEFAZ recusava com 1276);
  - `gIBSSN` opcional;
  - `gTribSN` junto do `gIBSCBS`.
- **`vItem` a partir de 2027:** soma IBS, CBS e IS ao valor do item (regra VB01-10 do MOC). Antes substituía o valor pelos tributos.
- **CNPJ alfanumérico (NT 2026.004)** em todas as pastas de schema: `Id` da NF-e, QR Code, `Id` de todos os eventos e da inutilização, `TCnpjVar` e Distribuição DF-e v1.04.
- **Eventos da Reforma:** `e211110` atualizado para o pacote de eventos v1.40.

## Situação das Notas Técnicas

Conferido em **20/11/2026** contra o [Portal Nacional da NF-e](https://www.nfe.fazenda.gov.br/portal/listaConteudo.aspx?tipoConteudo=BMPFMBoln3w=), o [MOC online](https://moc.sped.fazenda.pr.gov.br/NFe_NFCe/VisaoGeral.html) e os [avisos da SVRS](https://dfe-portal.svrs.rs.gov.br/NFE/Avisos). Os schemas foram comparados arquivo a arquivo com todos os pacotes vigentes do portal.

### Leiautes (`schemes/`)

| Pasta | Uso |
|---|---|
| `PL_009_V4` | Leiaute sem a Reforma Tributária (padrão). |
| `PL_010h` | Leiaute vigente com a Reforma (IBS/CBS/IS), NT 2026.005, 2026.006 e 2026.008. **Recomendado.** |
| `PL_010_V1.30`, `PL_010g` | Versões anteriores do PL_010, mantidas por compatibilidade. |
| `PL_010_V1`, `PL_010_V1.21` | Históricas. |

### Notas Técnicas

**Colunas:**

- **Lib** é esta biblioteca.
- **API** é a API Fiscal do ValuorERP, que monta o XML com esta biblioteca.

**Legenda:**

- ✅ implementado;
- 🟡 parcial;
- ❌ não implementado;
- — não se aplica (regra só da SEFAZ, sem campo novo).

| NT | Versão | Assunto | Lib | API | Observação |
|---|---|---|---|---|---|
| 2026.010 | 1.00 | DANFE com IBS/CBS/IS | ❌ | ❌ | DANFE ainda não mostra os tributos da Reforma. Produção em 01/12/2026. |
| 2026.009 | 1.00 | Regra I08-140 (CFOP 1.949/2.949) | — | — | Só validação da SEFAZ. |
| 2026.008 | 1.01 | Valor líquido do produto e `ICMSPrevistoPagtoAntecip` | ✅ | 🟡 | `ICMSPrevistoPagtoAntecip` em uso. Falta enviar `vUnComLiq`/`vProdLiq` (obrigatoriedade futura). Produção em 14/12/2026. |
| 2026.007 | 1.11 | Contribuinte exclusivo do IBS/CBS (emitente sem IE) | ✅ | 🟡 | Emitente sem IE aceito no PL_010. Falta encaminhar à SVRS. |
| 2026.006 | 1.01 | Vínculo com a transação de pagamento (split payment, grupo YC e evento 110300) | ✅ | ✅ | Produção adiada para 14/12/2026 (aviso SVRS/CGIBS de 12/11/2026). |
| 2026.005 | 1.00 | Simples Nacional na Reforma: `tpRegimeIBSCBS`, `cAtivSN`, `gTribSN`, evento 412140 | ✅ | ✅ | Produção em 14/12/2026. O 412140 é gerado pelo Fisco ("implementação futura"); a API só o lê na consulta. |
| 2026.004 | 1.01 | CNPJ alfanumérico | ✅ | ✅ | CNPJ de teste em homologação: `PC3D315K000193` (IE `0018001360`). |
| 2026.002 / 2026.003 | 1.11 / 1.00 | DANFE Simplificado Tipo 2 (`tpImp=6`) | ✅ | 🟡 | XML pronto. Falta o layout de impressão. |
| 2026.001 | 1.02b | Provedor de Assinatura e Autorização (PAA) | ❌ | ❌ | Opcional, só para quem emite pelo PAA. |
| 2025.002 | 1.53 | Reforma Tributária (IBS/CBS/IS) | ✅ | ✅ | NF-e, NFC-e e CT-e montam o grupo `IBSCBS`. |
| 2024.003 | 1.10 | Agro: guias e defensivos (grupo ZF) | ✅ | ✅ | |
| 2023.003 | 1.40 | Regras de CFOP na NFC-e | — | ✅ | |
| 2022.002 | 1.30a | Equiparação à exportação | — | — | Só validação da SEFAZ. |
| 2021.003 | 1.50 | Regras do GTIN | — | ✅ | |
| 2020.001 | 1.60 | Manifestação do destinatário | ✅ | ✅ | |
| 2014.002 | 1.40 | Distribuição DF-e | ✅ | ✅ | Schemas do pacote v1.04 (03/07/2026). |
| 2014.001 | 1.41 | EPEC | ✅ | 🟡 | NF-e em uso; EPEC da NFC-e (SP) só na biblioteca. |

## Instalação

Pelo Packagist (projeto original):

```bash
composer require nfephp-org/sped-nfe
```

Por este fork, fixado em um commit (é assim que a API Fiscal usa):

```json
{
    "repositories": [
        { "type": "vcs", "url": "https://github.com/wandersonmaracaipe/sped-nfe" }
    ],
    "require": {
        "nfephp-org/sped-nfe": "dev-master#<commit> as 5.2.5"
    }
}
```

Para usar a branch de desenvolvimento (`dev-master`), o `composer.json` do seu projeto precisa aceitar pacotes instáveis:

```json
"minimum-stability": "dev",
"prefer-stable": true
```

## Requisitos

- PHP 7.4 ou superior (recomendado 8.2 ou 8.3)
- Extensões: `dom`, `json`, `libxml`, `openssl`, `simplexml`, `soap`, `zlib`
- Dependências instaladas pelo Composer: [sped-common](https://github.com/nfephp-org/sped-common), [sped-gtin](https://github.com/nfephp-org/sped-gtin) e `justinrainbow/json-schema`
- Certificado digital A1 (`.pfx`) do emitente

Pacotes que costumam acompanhar esta biblioteca (opcionais):

| Pacote | Para quê |
|---|---|
| [sped-da](https://github.com/nfephp-org/sped-da) | DANFE, DANFCE e demais documentos impressos |
| [sped-mail](https://github.com/nfephp-org/sped-mail) | Envio das notas por e-mail |
| [sped-ibpt](https://github.com/nfephp-org/sped-ibpt) | Tributos aproximados (Lei 12.741) |
| [sped-gnre](https://github.com/nfephp-org/sped-gnre) | Guias GNRE |
| [posprint](https://github.com/nfephp-org/posprint) | Impressão em impressoras térmicas |

## Uso básico

A biblioteca usa *namespaces* e o autoload do Composer. Não inclua os arquivos com `require` diretamente.

```php
require __DIR__ . '/vendor/autoload.php';

use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;

// Montagem do XML com o leiaute desejado (PL_009_V4 sem Reforma, PL_010h com Reforma)
$nfe = new Make('PL_010h');
// ... tagide(), tagemit(), tagprod(), tagIBSCBS() ...
$xml = $nfe->getXML();

// Assinatura e transmissão
$config = json_encode([
    'atualizacao' => date('Y-m-d H:i:s'),
    'tpAmb'       => 2,          // 1 = produção, 2 = homologação
    'razaosocial' => 'EMPRESA TESTE LTDA',
    'siglaUF'     => 'SP',
    'cnpj'        => '00000000000000',
    'schemes'     => 'PL_010h',
    'versao'      => '4.00',
]);
$tools = new Tools($config, Certificate::readPfx(file_get_contents('certificado.pfx'), 'senha'));
$xmlAssinado = $tools->signNFe($xml); // valida o XML contra o XSD do leiaute antes de assinar
```

O guia completo está em [docs/StartGuide.md](docs/StartGuide.md).

## Estados atendidos

- **NF-e (modelo 55):** todas as UF.
- **NFC-e (modelo 65):** todas as UF.
- **NF-e emitida com e-CPF (pessoa física):** CE, PR e SP **não aceitam**. Em AM e GO não foi possível verificar. As demais UF aparentemente aceitam.

## Documentação

| Assunto | Arquivo |
|---|---|
| Primeiros passos | [StartGuide.md](docs/StartGuide.md) |
| Configuração | [Config.md](docs/Config.md) |
| Montagem do XML | [Make.md](docs/Make.md) · [MakePL010.md](docs/MakePL010.md) (Reforma) |
| Pagamentos | [TagPag.md](docs/TagPag.md) |
| Eventos da Reforma | [EventosRTC.md](docs/EventosRTC.md) |
| Cancelamento por substituição | [CancelamentoSubs.md](docs/CancelamentoSubs.md) |
| Contingência | [Contingency.md](docs/Contingency.md) |
| QR Code da NFC-e | [QRCode.md](docs/QRCode.md) |
| Conversão de TXT | [Convert.md](docs/Convert.md) · [EstruturaTxt.md](docs/EstruturaTxt.md) |
| Leitura do retorno da SEFAZ | [Standardize.md](docs/Standardize.md) |
| Certificado de teste | [GerarCertificadoAutoAssinadoParaTestes.md](docs/GerarCertificadoAutoAssinadoParaTestes.md) |
| Todas as funcionalidades | [Funcionalidades.md](docs/Funcionalidades.md) |

Dúvidas de uso: [grupo NFePHP](http://groups.google.com/group/nfephp), em vez de abrir uma issue. Histórico de mudanças: [CHANGELOG.md](CHANGELOG.md).

## Contribuindo

Leia o [CONTRIBUTING](CONTRIBUTING.md) e o [Código de Conduta](CONDUCT.md).

1. Faça um fork e clone o seu fork.
2. Ligue o projeto original como `upstream` e sincronize antes de começar:

   ```bash
   git remote add upstream git@github.com:nfephp-org/sped-nfe.git
   git fetch upstream && git merge upstream/master
   ```

3. Rode `composer install` (PHP 8.2 ou 8.3).
4. Para testes manuais com dados reais, use uma pasta `local/` na raiz. Ela não é enviada ao repositório.
5. Antes de enviar, rode a verificação completa:

   ```bash
   composer phpcbf   # corrige o estilo
   composer phpcs    # confere o estilo (PSR-12)
   composer stan     # análise estática
   composer test     # PHPUnit
   ```

6. Envie ao seu fork. O GitHub Actions repete as verificações, e com elas passando você abre o pull request.

Problemas de segurança: escreva direto aos mantenedores, sem abrir issue.

## Créditos e licença

- **Projeto original:** Roberto L. Machado e a comunidade [NFePHP](https://github.com/nfephp-org).
- **Fork:** mantido para o ValuorERP.

Licenciado sob LGPLv3 ou MIT. Veja [LICENSE.md](LICENSE.md).
