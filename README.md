# SPED-NFE — Fork Valuor

> **Fork de [`nfephp-org/sped-nfe`](https://github.com/nfephp-org/sped-nfe) mantido pela Valuor**
> para uso na **API Fiscal** do ValuorERP enquanto a NFePHP não publica o release oficial das NTs.

## ⚙️ Customizações deste fork

| NT | O que foi alterado | Status |
|----|--------------------|--------|
| **NT 2026.004** — CNPJ Alfanumérico | Schemas XSD aceitam CNPJ alfanumérico: `TCnpj` → `[A-Z0-9]{12}[0-9]{2}`, `TCnpjVar` → `[A-Z0-9]{3,14}`, chave de acesso (`TChNFe`/`Id`/QR) → `[0-9]{6}[A-Z0-9]{12}[0-9]{26}`, nos `tiposBasico_v4.00.xsd` + `leiauteNFe_v4.00.xsd`/`prod_*`. O DV da chave já é alfanumérico no `sped-common` (`Keys::verifyingDigit` usa `ord(c)-48`). | ✅ master (PR #1) |

Detalhes em [`CNPJ_ALFANUMERICO.md`](CNPJ_ALFANUMERICO.md).

> **Solução definitiva:** substituir os schemes pelo pacote XSD oficial da NT 2026.004 quando a SEFAZ/
> NFePHP publicar. Este patch destrava a emissão/validação com CNPJ alfanumérico enquanto isso.

## 📦 Como a API Fiscal consome este fork

No `composer.json` da API Fiscal (`fiscal-api/`):
```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/wandersonmaracaipe/sped-nfe" }
],
"require": {
    "nfephp-org/sped-nfe": "dev-master as 5.2.5"
}
```
O `composer.lock` fixa o commit exato. Para promover uma alteração deste fork à API:
`cd fiscal-api && bash bin/atualizar-fork.sh --push` → redeploy do container fiscal.

## 🔄 Manutenção (incorporar o upstream oficial)
```bash
git remote add upstream https://github.com/nfephp-org/sped-nfe   # uma vez
git fetch upstream && git merge upstream/master                  # resolve conflitos do patch se houver
git push origin master
```

---

# SPED-NFE

Biblioteca PHP para **geração, assinatura e comunicação** de NF-e (modelo 55) e NFC-e (modelo 65)
com as SEFAZ autorizadoras, no âmbito do projeto SPED da Receita.

![PHP Supported Version][ico-php]
![Actions](https://github.com/nfephp-org/sped-nfe/actions/workflows/ci.yml/badge.svg)
[![codecov](https://codecov.io/gh/nfephp-org/sped-nfe/branch/master/graph/badge.svg?token=UsZnjTNKKh)](https://codecov.io/gh/nfephp-org/sped-nfe)
[![Latest Stable Version][ico-stable]][link-packagist]
[![License][ico-license]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

## Índice

- [Conformidade (NTs e schemas)](#conformidade-nts-e-schemas)
- [Estados atendidos](#estados-atendidos)
- [Instalação](#instalação)
- [Requisitos](#requisitos)
- [Pacotes complementares](#pacotes-complementares)
- [Uso](#uso)
- [Documentação](#documentação)
- [Como contribuir](#como-contribuir)
- [Testes](#testes)
- [Segurança](#segurança)
- [Créditos e licença](#créditos-e-licença)

## Conformidade (NTs e schemas)

Notas Técnicas e schemas (Pacotes de Liberação) suportados:

**Reforma Tributária do Consumo (RTC) — IBS/CBS/IS**
- NT 2025.002-RTC v.1.01 / v.1.10 / v.1.20 — adequações de leiaute da NF-e e NFC-e
- Schemas `PL_010` v1.10b (09/06/2025), v1.20b (30/07/2025) e `PL_010C` v1.30

**NFC-e / QR-Code**
- NT 2025.001 v.1.00 — Simplificação Operacional: QR-Code versão 3 e envio síncrono

**Regras de validação e leiautes**
- NT 2024.003 v.1.04 / v.1.05 — alteração de regras de validação e **Produtos da Agricultura,
  Pecuária e Produção Florestal** (grupo ZF)
- NT 2023.001 v.1.60 — Tributação Monofásica sobre Combustíveis
- NT 2021.003 v.1.40 — Validação de GTIN

> Customizações específicas deste fork: ver a seção **Fork Valuor** no topo.

## Estados atendidos

- **NF-e (modelo 55):** todos
- **NFC-e (modelo 65):** todos
- **NF-e com eCPF (emissor pessoa física):** aceito na maioria das UFs.
  - ❌ **CE, PR e SP** não aceitam emissão com eCPF.
  - ⚠️ **AM e GO** não foi possível verificar (problemas de comunicação).

## Instalação

Pacote distribuído via [Composer](https://getcomposer.org/) (listado no [Packagist][link-packagist]).

**Versão estável:**
```bash
composer require nfephp-org/sped-nfe
```

**Versão em desenvolvimento (branch master):**
```bash
composer require nfephp-org/sped-nfe:dev-master
```
> Para usar a `dev-master`, ajuste `"minimum-stability": "dev"` no `composer.json` da sua aplicação.

## Requisitos

- **PHP ≥ 7.4** (confira sempre o badge de versão suportada)
- Extensões: `curl`, `dom`, `json`, `gd`, `mbstring`, `mcrypt`, `openssl`, `soap`, `xml`, `zip`
- [`nfephp-org/sped-common`](https://github.com/nfephp-org/sped-common)

## Pacotes complementares

Opcionais, para outras ações do SPED:

| Pacote | Para quê |
|--------|----------|
| [sped-da](https://github.com/nfephp-org/sped-da) | Documentos impressos (DANFE, DACTE, …) |
| [sped-mail](https://github.com/nfephp-org/sped-mail) | Envio das notas por e-mail |
| [sped-ibpt](https://github.com/nfephp-org/sped-ibpt) | Impostos aproximados (Lei 12.741) |
| [sped-gnre](https://github.com/nfephp-org/sped-gnre) | Geração da GNRE |
| [posprint](https://github.com/nfephp-org/posprint) | Impressão em impressoras térmicas POS |

## Uso

A biblioteca usa **namespaces** (PSR-4). Carregue o autoload do Composer e importe as classes:

```php
require VENDOR_DIR . 'autoload.php';   // pasta vendor da sua instalação Composer

use NFePHP\NFe\Make;

$nfe = new Make();
// ... montar, assinar e transmitir ...
```

> ❌ **Não** inclua arquivos da `src/` diretamente (`require 'sped-nfe/src/Make.php'`) — use sempre o
> autoload do Composer e os namespaces.

## Documentação

A documentação está em evolução: [Funcionalidades](docs/Funcionalidades.md).

Para dúvidas, inscreva-se no [grupo NFePHP no Google Groups](http://groups.google.com/group/nfephp)
(em vez de abrir uma issue).

## Como contribuir

Veja [CONTRIBUTING](CONTRIBUTING.md) e o [Código de Conduta](CONDUCT.md). Em resumo:

1. Faça um **fork** do projeto na sua conta.
2. Clone o seu fork na máquina de desenvolvimento (PHP 8.2/8.3 recomendado) e rode `composer install`.
3. Adicione o repositório original como `upstream` e sincronize antes de codar:
   ```bash
   git remote add upstream git@github.com:nfephp-org/sped-nfe.git
   git fetch upstream && git merge upstream/master && git push
   ```
4. Implemente sua alteração. Para testar com dados sensíveis, use uma pasta `local/` na raiz
   (não é versionada).
5. Antes de enviar, rode a verificação completa:
   ```bash
   composer phpcbf && composer phpcs && composer stan && composer test
   ```
6. Passando, envie ao seu fork e abra um **pull request** para o projeto original.

## Testes

Os testes rodam com **PHPUnit** (`composer test`).

## Segurança

Encontrou uma vulnerabilidade? Envie um e-mail **diretamente aos mantenedores** do pacote — não abra
uma issue pública.

## Créditos e licença

- **Roberto L. Machado** — owner e desenvolvedor original.
- A todos que colaboram com o desenvolvimento contínuo da biblioteca.

Distribuído sob **LGPLv3** ou **MIT** — ver [LICENSE.md](LICENSE.md).

Aderente a [PSR-1], [PSR-2] e [PSR-4].

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md

[ico-php]: https://img.shields.io/packagist/php-v/nfephp-org/sped-da
[ico-stable]: https://poser.pugx.org/nfephp-org/sped-nfe/version
[ico-downloads]: https://img.shields.io/packagist/dt/nfephp-org/sped-nfe.svg?style=flat-square
[ico-license]: https://poser.pugx.org/nfephp-org/nfephp/license.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/nfephp-org/sped-nfe
[link-downloads]: https://packagist.org/packages/nfephp-org/sped-nfe
