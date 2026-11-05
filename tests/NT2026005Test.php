<?php

declare(strict_types=1);

namespace NFePHP\NFe\Tests;

use NFePHP\NFe\Make;
use PHPUnit\Framework\TestCase;

class NT2026005Test extends TestCase
{
    private Make $make;

    protected function setUp(): void
    {
        $this->make = new Make('PL_010');
    }

    public function testTpRegimeIBSCBSNoEmit(): void
    {
        $std = new \stdClass();
        $std->CNPJ = '12345678000195';
        $std->xNome = 'EMPRESA SIMPLES NACIONAL LTDA';
        $std->xFant = 'SIMPLES TESTE';
        $std->IE = '123456789';
        $std->CRT = '1';
        $std->tpRegimeIBSCBS = 1; // NT 2026.005 (C21a)

        $emit = $this->make->tagemit($std);

        $this->assertInstanceOf(\DOMElement::class, $emit);
        $tpRegime = $emit->getElementsByTagName('tpRegimeIBSCBS')->item(0);
        $this->assertNotNull($tpRegime, 'Tag tpRegimeIBSCBS deve existir em <emit>');
        $this->assertSame('1', $tpRegime->nodeValue);
    }

    public function testCAtivSNNoProd(): void
    {
        $std = new \stdClass();
        $std->item = 1;
        $std->cProd = 'PROD001';
        $std->cEAN = 'SEM GTIN';
        $std->xProd = 'PRODUTO TESTE SIMPLES';
        $std->NCM = '21069090';
        $std->CFOP = '5102';
        $std->uCom = 'UN';
        $std->qCom = 1.0000;
        $std->vUnCom = 100.00;
        $std->vProd = 100.00;
        $std->cEANTrib = 'SEM GTIN';
        $std->uTrib = 'UN';
        $std->qTrib = 1.0000;
        $std->vUnTrib = 100.00;
        $std->indTot = 1;
        $std->cAtivSN = '01'; // NT 2026.005 (I17d)

        $prod = $this->make->tagprod($std);

        $this->assertInstanceOf(\DOMElement::class, $prod);
        $cAtiv = $prod->getElementsByTagName('cAtivSN')->item(0);
        $this->assertNotNull($cAtiv, 'Tag cAtivSN deve existir em <prod>');
        $this->assertSame('01', $cAtiv->nodeValue);
    }

    public function testGTribSNNoItemEGroupTotal(): void
    {
        // 1. infNFe & ide
        $stdInf = new \stdClass();
        $stdInf->versao = '4.00';
        $this->make->taginfNFe($stdInf);

        $stdIde = new \stdClass();
        $stdIde->cUF = '35';
        $stdIde->cNF = '12345678';
        $stdIde->natOp = 'VENDA MERCADORIA';
        $stdIde->mod = '55';
        $stdIde->serie = 1;
        $stdIde->nNF = 1;
        $stdIde->dhEmi = '2026-11-05T12:00:00-03:00';
        $stdIde->tpNF = 1;
        $stdIde->idDest = 1;
        $stdIde->cMunFG = '3550308';
        $stdIde->tpImp = 1;
        $stdIde->tpEmis = 1;
        $stdIde->tpAmb = 2;
        $stdIde->finNFe = 1;
        $stdIde->indFinal = 1;
        $stdIde->indPres = 1;
        $stdIde->procEmi = 0;
        $stdIde->verProc = '1.0';
        $this->make->tagide($stdIde);

        // 2. emit com CRT=1 e tpRegimeIBSCBS=1
        $stdEmit = new \stdClass();
        $stdEmit->CNPJ = '12345678000195';
        $stdEmit->xNome = 'EMPRESA TESTE SN';
        $stdEmit->IE = '123456789';
        $stdEmit->CRT = '1';
        $stdEmit->tpRegimeIBSCBS = 1;
        $this->make->tagemit($stdEmit);

        $stdEnder = new \stdClass();
        $stdEnder->xLgr = 'Rua Teste';
        $stdEnder->nro = '123';
        $stdEnder->xBairro = 'Centro';
        $stdEnder->cMun = '3550308';
        $stdEnder->xMun = 'Sao Paulo';
        $stdEnder->UF = 'SP';
        $stdEnder->CEP = '01001000';
        $this->make->tagenderEmit($stdEnder);

        // 3. dest
        $stdDest = new \stdClass();
        $stdDest->CPF = '12345678901';
        $stdDest->xNome = 'CONSUMIDOR FINAL';
        $stdDest->indIEDest = '9';
        $this->make->tagdest($stdDest);

        // 4. prod com cAtivSN
        $stdProd = new \stdClass();
        $stdProd->item = 1;
        $stdProd->cProd = 'SKU01';
        $stdProd->cEAN = 'SEM GTIN';
        $stdProd->xProd = 'PRODUTO SIMPLES';
        $stdProd->NCM = '21069090';
        $stdProd->CFOP = '5102';
        $stdProd->uCom = 'UN';
        $stdProd->qCom = 2;
        $stdProd->vUnCom = 50.00;
        $stdProd->vProd = 100.00;
        $stdProd->cEANTrib = 'SEM GTIN';
        $stdProd->uTrib = 'UN';
        $stdProd->qTrib = 2;
        $stdProd->vUnTrib = 50.00;
        $stdProd->indTot = 1;
        $stdProd->cAtivSN = '01';
        $this->make->tagprod($stdProd);

        // 5. imposto IBSCBS com gTribSN
        $stdIBS = new \stdClass();
        $stdIBS->item = 1;
        $stdIBS->CST = '01';
        $stdIBS->cClassTrib = '01';
        $this->make->tagIBSCBS($stdIBS);

        $stdSN = new \stdClass();
        $stdSN->item = 1;
        $stdSN->pIBSSN = 2.5000;
        $stdSN->vIBSSN = 2.50;
        $stdSN->pCBSSN = 0.8500;
        $stdSN->vCBSSN = 0.85;
        $this->make->taggTribSN($stdSN);

        // 6. ICMSSN
        $stdICMS = new \stdClass();
        $stdICMS->item = 1;
        $stdICMS->orig = 0;
        $stdICMS->CSOSN = '102';
        $this->make->tagICMSSN($stdICMS);

        // 7. Renderiza XML
        $xml = $this->make->getXML();

        $this->assertNotEmpty($xml);
        $this->assertStringContainsString('<tpRegimeIBSCBS>1</tpRegimeIBSCBS>', $xml);
        $this->assertStringContainsString('<cAtivSN>01</cAtivSN>', $xml);
        $this->assertStringContainsString('<gTribSN>', $xml);
        $this->assertStringContainsString('<pIBSSN>2.5000</pIBSSN>', $xml);
        $this->assertStringContainsString('<vIBSSN>2.50</vIBSSN>', $xml);
        $this->assertStringContainsString('<pCBSSN>0.8500</pCBSSN>', $xml);
        $this->assertStringContainsString('<vCBSSN>0.85</vCBSSN>', $xml);

        // Verifica totalização no grupo W59h <total><IBSCBSTot><gTribSN>
        $dom = new \DOMDocument();
        $dom->loadXML($xml);
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('nfe', 'http://www.portalfiscal.inf.br/nfe');

        $nodes = $xpath->query('//nfe:total/nfe:IBSCBSTot/nfe:gTribSN');
        $this->assertGreaterThan(0, $nodes->length, 'Totalizador IBSCBSTot deve conter nó gTribSN');

        $totalIBSSN = $xpath->query('//nfe:total/nfe:IBSCBSTot/nfe:gTribSN/nfe:vIBSSN')->item(0);
        $totalCBSSN = $xpath->query('//nfe:total/nfe:IBSCBSTot/nfe:gTribSN/nfe:vCBSSN')->item(0);

        $this->assertNotNull($totalIBSSN);
        $this->assertNotNull($totalCBSSN);
        $this->assertSame('2.50', $totalIBSSN->nodeValue);
        $this->assertSame('0.85', $totalCBSSN->nodeValue);
    }
}
