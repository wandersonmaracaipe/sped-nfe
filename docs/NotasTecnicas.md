# Notas Técnicas da NF-e/NFC-e — situação no sped-nfe (fork Valuor) e na API Fiscal

Conferido em **09/10/2026** contra a lista oficial do Portal Nacional da NF-e
(<https://www.nfe.fazenda.gov.br/portal/listaConteudo.aspx?tipoConteudo=04BIflQt1aY=>), o código deste
repositório (`src/`, `schemes/`) e o uso na API Fiscal (`fiscal-api`, que instala este fork pelo
commit `a5fe801`).

## Como ler a tabela

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

## NTs vigentes publicadas em 2026

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

## Eventos da Reforma (NT 2025.002, item 8)

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

## NTs anteriores ainda vigentes

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

## Prioridades sugeridas

1. **NT 2026.006 (split payment):** produção em 03/11/2026.
2. **NT 2020.006 (`indIntermed`):** rejeição na NF-e não presencial.
3. **NT 2025.002:**
   - schema/Make da v1.40 em diante (`ISUFemit`, `tpNFCredito` 06);
   - monofásica de combustíveis da v1.50.
4. **NT 2026.010 (DANFE com IBS/CBS):** produção em 01/12/2026.
5. **NT 2026.008:** valor líquido do produto.
6. **Impressão do DANFE Simplificado Tipo 2 (2026.003)** e encaminhamento à SVRS do emitente só IBS/CBS (2026.007).
