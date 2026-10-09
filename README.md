# SPED-NFE 

Biblioteca para geração e comunicação das NFe com as SEFAZ autorizadoras, e visa fornecer os meios para gerar, assinar e enviar os dados relativos ao projeto Sped NFe das SEFAZ.

## Atualizado — Notas Técnicas

Conferido em **09/10/2026** contra a lista oficial do Portal Nacional da NF-e
(<https://www.nfe.fazenda.gov.br/portal/listaConteudo.aspx?tipoConteudo=04BIflQt1aY=>), o código deste
repositório (`src/`, `schemes/`) e o uso na API Fiscal (`fiscal-api`, que instala este fork pelo
commit `a5fe801`).

### Como ler a tabela

**Colunas:**
- **Lib**: este sped-nfe.
- **API**: a `fiscal-api`, que monta o XML e expõe os serviços para o ERP.

**Situações:**
- ✅ implementado e em uso;
- 🟡 parcial (o que falta está descrito);
- 📚 só na biblioteca (a API não usa nem expõe);
- ❌ não implementado;
- — não se aplica (regra validada pela SEFAZ, sem campo novo para o emissor, ou operação fora do perfil).

Leiaute em uso: **PL_009_V4** (sem Reforma) e **PL_010_V1.30** (com IBS/CBS). A pasta `PL_010_V1.30` já
traz parte da v1.40 (`cIndOp`, `refDFeAnt`, IE do emitente opcional, `infPAA`).

### NTs vigentes publicadas em 2026

| NT | Versão | Assunto | Lib | API | O que falta |
|---|---|---|---|---|---|
| 2025.002-RTC | 1.52 | Reforma Tributária (IBS/CBS/IS) | 🟡 | 🟡 | Leiaute até a v1.30, com partes da v1.40. Faltam: `ISUFemit` (C22); `tpNFCredito` 06 (retorno por recusa parcial, v1.36); a reformulação da monofásica de combustíveis (v1.50). Os eventos estão completos (ver abaixo). |
| 2026.010 | 1.00 | DANFE da Reforma (IBS/CBS/IS no DANFE) | ❌ | ❌ | O DANFE (`DanfeValuor`/sped-da) não mostra IBS/CBS/IS. Produção em 01/12/2026. |
| 2026.008 | 1.00 | Valor líquido do produto (`vUnComLiq`, `vProdLiq`, `vProdLiqTot`) e `ICMSPrevistoPagtoAntecip` | ❌ | ❌ | Campos novos (hoje opcionais; "futuramente obrigatórios"). |
| 2026.007 | 1.10 | Emissão por contribuinte exclusivo do IBS/CBS (emitente sem IE), LCC/CCC | 🟡 | 🟡 | O schema aceita emitente sem IE. Falta encaminhar esse emitente à SVRS (autorização centralizada). |
| 2026.006 | 1.00 | Vinculação com a transação de pagamento (split payment): grupo YC e evento 110300 | ❌ | ❌ | Homologação em 05/10/2026, **produção em 03/11/2026**. |
| 2026.009 | 1.00 | Regra I08-140: CFOP 1.949/2.949 | — | — | Só regra da SEFAZ, sem mudança de leiaute. |
| 2026.004 | 1.01 | CNPJ alfanumérico no schema | ✅ | ✅ | XSD com CNPJ alfanumérico. A API normaliza preservando letras (`App\Support\Cnpj`). |
| 2026.002 / 2026.003 | 1.11 / 1.00 | DANFE Simplificado Tipo 2 (`tpImp=6`) | ✅ | 🟡 | O XML aceita `tpImp=6` (cStat 120 incluído). Falta o **layout de impressão** do DANFE Simplificado Tipo 2. |
| 2026.001 | 1.02b | Provedor de Assinatura e Autorização (PAA) | ❌ | ❌ | O XSD tem `infPAA`, mas a lib não monta. É opcional (só para quem emite pelo PAA). |
| 2021.003 | 1.50 | Regras do GTIN | — | ✅ | Regras da SEFAZ. O ERP valida o dígito verificador e manda "SEM GTIN" quando o código é inválido. |
| 2023.003 | 1.40 | Regras de CFOP na NFC-e | — | ✅ | O ERP troca o CFOP pelo aceito na NFC-e (`CfopNfce`). |
| 2014.001 | 1.41 | EPEC | ✅ | 🟡 | NF-e: `POST /nfe/epec`. A EPEC da NFC-e (SP) existe só na lib. |
| 2014.002 | 1.40 | Distribuição DF-e | ✅ | ✅ | `/manifesto/novosDocumentos` (NF destinadas). |
| 2024.003 | 1.10 | Agro: guias e defensivos (grupo ZF) | ✅ | ✅ | `tagAgropecuarioGuia`/`Defensivo`. |
| 2020.001 | 1.60 | Manifestação do destinatário | ✅ | ✅ | `/manifesto/manifestar`. |
| 2022.002 | 1.30a | Equiparação à exportação | — | — | Regras da SEFAZ. |

### Eventos da Reforma (NT 2025.002, item 8)

| Evento | Método no sped-nfe | API |
|---|---|---|
| 112110 Pagamento integral | `sefazInfoPagtoIntegral` | ✅ |
| 112120 ALC/ZFM não convertida em isenção | `sefazImportacaoZFM` | ✅ |
| 112130 Perecimento/perda no transporte (fornecedor) | `sefazRouboPerdaTransporteFornecedor` | ✅ |
| 112140 Fornecimento não realizado | `sefazFornecimentoNaoRealizado` | ✅ |
| 112150 Atualização da previsão de entrega | `sefazAtualizacaoDataEntrega` | ✅ |
| 211110 Apropriação de crédito presumido | `sefazSolApropCredPresumido` | ✅ |
| 211124 Perecimento/perda no transporte (adquirente) | `sefazRouboPerdaTransporteAdquirente` | ✅ |
| 211128 Aceite de débito em nota de crédito | `sefazAceiteDebito` | ✅ |
| 211130 Imobilização de item | `sefazImobilizacaoItem` | ✅ |
| 211140 Apropriação de crédito de combustível | `sefazApropriacaoCreditoComb` | ✅ |
| 211150 Crédito de bens/serviços do adquirente | `sefazApropriacaoCreditoBens` | ✅ |
| 212110 / 212120 Transferência de crédito IBS/CBS (sucessão) | `sefazManifestacaoTransfCredIBS` / `CBS` | ✅ |
| 110001 Cancelamento de evento | `sefazCancelaEvento` | ✅ |

**Correção em 09/10/2026:**
- 6 dos 9 eventos que a API expunha chamavam métodos com nome antigo (`sefazImportacaoALCZFM`, `sefazPerecimento…`, `sefazAtualizacaoDataPrevisaoEntrega`, `sefazAceiteDebitoNotaCredito`, `sefazSolicitacaoCreditoCombustivel`) e davam erro fatal.
- Dois códigos não existem na NT (212160 e 212170; os certos são 211128 e 211140).
- O 211120 foi eliminado na v1.40 e não é exposto.

### NTs anteriores ainda vigentes

| NT | Versão | Assunto | Lib | API | Observação |
|---|---|---|---|---|---|
| Conjunta 2025.001 | — | CNPJ alfanumérico (orientações) | ✅ | ✅ | Ver 2026.004. |
| 2025.001 | 1.03 | QR-Code v3 da NFC-e e regras | ✅ | ✅ | |
| 2023.001 | 1.60 | ICMS monofásico de combustíveis (CST 02/15/53/61) | ✅ | ✅ | `qBCMono`/`adRemICMS` no `tagICMS`. |
| 2023.002 | 1.01 | NFC-e por produtor rural pessoa física (emitente CPF), sem lote e sem denegação | ✅ | ✅ | |
| 2023.004 | 1.20 | Pagamento detalhado (`CNPJPag`/`UFPag`, `dPag`, `idTermPag`, `tpIntegra`) | ✅ | ✅ | |
| 2023.005 | 1.02 | Evento Insucesso na Entrega (110192) | ✅ | 📚 | |
| 2024.001 | 1.20 | Emissão por MEI (CRT 4) | ✅ | ✅ | |
| 2024.002 | 1.00 | Conciliação financeira (ECONF, 110750) | ✅ | 📚 | |
| 2022.005 / 2015.003 | 1.11 / 1.94 | DIFAL na venda interestadual a consumidor final (ICMSUFDest) | ✅ | ✅ | Só na NF-e. |
| 2022.003 | 1.11 | Novos campos (inclui origem do combustível, `origComb`) | ✅ | 🟡 | A API não envia `origComb`. |
| 2021.002 | 1.12 | Nota Fiscal Fácil (NFF) | ✅ | — | Fora do perfil. |
| 2021.001 | 1.01 | Evento Comprovante de Entrega (110130) | ✅ | 📚 | |
| 2020.007 | 1.40 | Evento Ator Interessado (110150) | ✅ | 📚 | |
| 2020.006 | 1.31 | Intermediador (`indIntermed`, `infIntermed`) | ✅ | ❌ | A API não envia `indIntermed`. Obrigatório quando a NF-e tem `indPres` 2, 3, 4 ou 9: hoje a NF-e não presencial pode ser rejeitada. |
| 2020.004 | 1.10 | DANFE Simplificado – Etiqueta | ❌ | ❌ | |
| 2022.001 | 1.00 | Web Service de consulta do GTIN | ❌ | ❌ | |
| 2018.005 | 1.52 | Responsável técnico (`infRespTec`) e CSRT | ✅ | 🟡 | `infRespTec` enviado; o hash do CSRT (exigido por algumas UFs) não. |
| 2018.004 | 1.00 | Cancelamento por substituição da NFC-e (110112) | ✅ | 📚 | |
| 2015.001 | 1.30 | Prorrogação da suspensão do ICMS (EPP/ECPP) | ✅ | 📚 | |
| 2016.002 | 1.61 | Leiaute 4.00 | ✅ | ✅ | Base atual. |
| 2017.002, 2018.003, 2016.001, 2016.003, 2020.002 | — | Tabelas de CFOP, países, unidades, NCM e enquadramento do IPI | — | 🟡 | O ERP importa a tabela NCM e confere o CFOP; países, unidades e enquadramento do IPI não são validados antes do envio. |
| 2019.001, 2020.005, 2021.004, 2022.004, 2018.001, 2018.002 | — | Só regras de validação da SEFAZ | — | — | O ERP faz pré-validação de parte delas (`FiscalPreValidacaoService`). |

### Prioridades sugeridas

1. **NT 2026.006 (split payment):** produção em 03/11/2026.
2. **NT 2020.006 (`indIntermed`):** rejeição na NF-e não presencial.
3. **NT 2025.002:**
   - schema/Make da v1.40 em diante (`ISUFemit`, `tpNFCredito` 06);
   - monofásica de combustíveis da v1.50.
4. **NT 2026.010 (DANFE com IBS/CBS):** produção em 01/12/2026.
5. **NT 2026.008:** valor líquido do produto.
6. **Impressão do DANFE Simplificado Tipo 2 (2026.003)** e encaminhamento à SVRS do emitente só IBS/CBS (2026.007).


![PHP Supported Version][ico-php]
![Actions](https://github.com/nfephp-org/sped-nfe/actions/workflows/ci.yml/badge.svg)
[![codecov](https://codecov.io/gh/nfephp-org/sped-nfe/branch/master/graph/badge.svg?token=UsZnjTNKKh)](https://codecov.io/gh/nfephp-org/sped-nfe)

[![Latest Stable Version][ico-stable]][link-packagist]
[![Latest Version on Packagist][ico-version]][link-packagist]
[![License][ico-license]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

[![Issues][ico-issues]][link-issues]
[![Forks][ico-forks]][link-forks]
[![Stars][ico-stars]][link-stars]

## Estados atendidos

### NFe (modelo 55) TODOS

### NFCe (modelo 65) Todos

### NFe com eCPF (emissor pessoa física)

> Os estados de **CE**, **PR** e **SP** **NÃO ACEITAM EMISSÃO com eCPF**

> AM e GO não foi possivel verificar por problemas na comunicação

> Todos os demais estados (aparentemente) já aceitam emissão por eCPF

Este pacote é aderente com os [PSR-1], [PSR-2] e [PSR-4]. Se você observar negligências de conformidade, por favor envie um patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md

Não deixe de se cadastrar no [grupo de discussão do NFePHP](http://groups.google.com/group/nfephp) para acompanhar o desenvolvimento e participar das discussões e tirar dúvidas!

## Install

**Este pacote está listado no [Packgist](https://packagist.org/) foi desenvolvido para uso do [Composer](https://getcomposer.org/), portanto não será explicitada nenhuma alternativa de instalação.**

*E deve ser instalado com:*
```bash
composer require nfephp-org/sped-nfe
```
Ou ainda alterando o composer.json do seu aplicativo inserindo:
```json
"require": {
    "nfephp-org/sped-nfe" : "^5.0"
}
```

*Para utilizar o pacote em desenvolvimento (branch master) deve ser instalado com:*
```bash
composer require nfephp-org/sped-nfe:dev-master
```

*Ou ainda alterando o composer.json do seu aplicativo inserindo:*
```json
"require": {
    "nfephp-org/sped-nfe" : "dev-master"
}
```

> NOTA: Ao utilizar este pacote na versão em desenvolvimento não se esqueça de alterar o composer.json da sua aplicação para aceitar pacotes em desenvolvimento, alterando a propriedade "minimum-stability" de "stable" para "dev".
> ```json
> "minimum-stability": "dev"
> ```

## Requirements

Para que este pacote possa funcionar são necessários os seguintes requisitos do PHP e outros pacotes dos quais esse depende.

- PHP 7.x (minimo PHP 7.4 veja sempre nos badges) 
- ext-curl
- ext-dom
- ext-json
- ext-gd
- ext-mbstring
- ext-mcrypt
- ext-openssl
- ext-soap
- ext-xml
- ext-zip
- [sped-common](https://github.com/nfephp-org/sped-common)

> Para outras ações necessárias ao SPED, podem ser usados (opcionalmente) outros pacotes, como:

> - [sped-da](https://github.com/nfephp-org/sped-da) Geração dos documentos impressos (DANFE, DACTE, etc.)
> - [sped-mail](https://github.com/nfephp-org/sped-mail) Envio de email com as notas e outros documentos fiscais 
> - [sped-ibpt](https://github.com/nfephp-org/sped-ibpt) Consulta dos impostos aproximados na venda a consumidor
> - [sped-gnre](https://github.com/nfephp-org/sped-gnre) Geração do GNRE
> - [posprint](https://github.com/nfephp-org/posprint) Impressão de documentos em impressoras térmicas POS


## Como eu faço uso desta biblioteca no meu projeto?

Primeiro, esta biblioteca faz uso dos recursos mais atuais do PHP para classes e objetos, portanto abaixo vai um exemplo ERRADO de uso:
```
require 'sped-nfe/src/Make.php';

$nfe = new Make();
```
Portanto, você deve primeiro entender que para usar esta biblioteca você precisará trabalhar com NAMESPACES pois trabalhamos com NAMESPACES.

Agora que você sabe que NAMESPACES é requerido, o uso correto para o exemplo acima seria:
```
// VENDOR_DIR = pasta vendor da sua instalação composer
require VENDOR_DIR . 'autoload.php';

use NFePHP\NFe\Make;

$nfe = new Make();
```

## Acknowledgments

- A todos os colegas que colaboram de alguma forma com o desenvolvimento contínuo desta biblioteca.


## Documentation

O processo de documentação ainda está no inicio, mas já existem alguns documentos úteis.

[Documentação](docs/Funcionalidades.md)

### Para tirar suas duvidas não inicie uma ISSUE, mas se inscreva no grupo do google [NFePHP](http://groups.google.com/group/nfephp).
 
## Contributing

Para contribuir com correções de BUGS, melhoria no código, documentação, elaboração de testes ou qualquer outro auxílio técnico e de programação por favor observe o [CONTRIBUTING](CONTRIBUTING.md) e o  [Código de Conduta](CONDUCT.md) para maiores detalhes.

### Etapas para contribuir com Código

1. Faça um fork do projeto em sua conta no GitHub
2. Baixe a biblioteca na sua maquina de desenvolvimento a partir do seu próprio fork
3. Execute o composer install na raiz do projeto (prefira usar o PHP 8.2 ou 8.3)
4. Crie uma relação com o projeto original usando o git, com isso criará um bloco denominado "upstream" com uma cópia do projeto original
```
git remote add upstream git@github.com:nfephp-org/sped-nfe.git
```
5. Antes de começar a codar sobre sua cópia sempre sincronize o projeto com o repositório principal
```
git fetch upstream
git merge upstream/master
git push
```
6. Agora pode codar sobre sua cópia da biblioteca
7. Ao terminar, sempre teste suas alterações para garantir o funcionamento, para isso recomendo criar uma pasta denominada "local" na raiz, e esta pasta não será enviada ao repositório, então poderá ter dados sensíveis.
8. Sempre, antes de fazer envio ao seu repositório execute os comandos abaixo, a partir de raiz do projeto na sua maquina: 
```
composer phpcbf
composer phpcs
composer stan
composer test
```
9. Se nenhum erro for indicado pos esses testes, pode enviar ao seu repositório, lá serão executados os comandos do GitHub Actions
10. Se passar nos testes, pode fazer um pull request para o projeto original.
11. Se o PR for aceito, não esqueça de repetir os comandos do passo 5.
 
## Change log

Acompanhe o [CHANGELOG](CHANGELOG.md) para maiores informações sobre as alterações recentes.

## Testing

Todos os testes são desenvolvidos para operar com o PHPUNIT

## Security

Caso você encontre algum problema relativo a segurança, por favor envie um email diretamente aos mantenedores do pacote ao invés de abrir um ISSUE.

## Credits

Roberto L. Machado (owner and developer)

## License

Este pacote está diponibilizado sob LGPLv3 ou MIT License (MIT). Leia  [Arquivo de Licença](LICENSE.md) para maiores informações.

[ico-php]: https://img.shields.io/packagist/php-v/nfephp-org/sped-da
[ico-stable]: https://poser.pugx.org/nfephp-org/sped-nfe/version
[ico-stars]: https://img.shields.io/github/stars/nfephp-org/sped-nfe.svg?style=flat-square
[ico-forks]: https://img.shields.io/github/forks/nfephp-org/sped-nfe.svg?style=flat-square
[ico-issues]: https://img.shields.io/github/issues/nfephp-org/sped-nfe.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/nfephp-org/sped-nfe.svg?style=flat-square
[ico-version]: https://img.shields.io/packagist/v/nfephp-org/sped-nfe.svg?style=flat-square
[ico-license]: https://poser.pugx.org/nfephp-org/nfephp/license.svg?style=flat-square


[link-packagist]: https://packagist.org/packages/nfephp-org/sped-nfe
[link-downloads]: https://packagist.org/packages/nfephp-org/sped-nfe
[link-author]: https://github.com/nfephp-org
[link-issues]: https://github.com/nfephp-org/sped-nfe/issues
[link-forks]: https://github.com/nfephp-org/sped-nfe/network
[link-stars]: https://github.com/nfephp-org/sped-nfe/stargazers
[link-gitter]: https://gitter.im/nfephp-org/sped-nfe?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge
