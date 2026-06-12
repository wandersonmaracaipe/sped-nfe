# Adequação CNPJ Alfanumérico (NT 2026.004 / NT Conjunta DFe 2025.001)

Fork da Valuor para aceitar CNPJ alfanumérico na emissão (uso na API Fiscal).

## Alterações nos schemas (tiposBasico_v4.00.xsd, leiauteNFe_v4.00.xsd, prod_*)
- `TCnpj`: `[0-9]{14}` → `[A-Z0-9]{12}[0-9]{2}` (12 posições alfanuméricas + 2 DV numéricos)
- `TCnpjVar`: `[0-9]{3,14}` → `[A-Z0-9]{3,14}`
- `TChNFe` e chave inline (`NFe...`, QR `chNFe=`): `[0-9]{44}` → `[0-9]{6}[A-Z0-9]{12}[0-9]{26}`
  (cUF+AAMM numéricos · CNPJ alfanumérico nas posições 7-20 · restante numérico)

O DV da chave já é alfanumérico no `sped-common` (`Keys::verifyingDigit` usa ord(c)-48).

> Solução definitiva: substituir os schemes pelo pacote XSD oficial da NT 2026.004
> quando publicado. Este patch destrava emissão/validação enquanto isso.
