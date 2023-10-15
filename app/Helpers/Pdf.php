<?php

namespace App\Helpers;

# https://tcpdf.org/
# https://www.xml-convert.com/en/convert-tff-font-to-afm-pfa-fpdf-tcpdf

use TCPDF;
use Illuminate\Support\Facades\Storage;

/*
Available CSS inline:
    font-family
    font-size
    font-weight
    font-style
    color
    background-color
    text-decoration
    width
    height
    text-align
*/

class Pdf
{
    private string $_orientation = 'P';
    private string $_unit = 'cm';
    private string $_title;
    private string $_viewName;
    private array $_viewParams;
    public const FONT_NAME = 'barlowreg';

    /**
     * @throws \Exception
     */
    public function __construct(string $title, string $viewName, array $viewParams=[])
    {
        if (!view()->exists($viewName)) {
            throw new \Exception('View não encontrada para gerar PDF!');
        }

        $this->_title = $title;
        $this->_viewName = $viewName;
        $this->_viewParams = $viewParams;
    }

    public function setPortrait(): void
    {
        $this->_orientation = 'P';
    }

    public function setLandscape(): void
    {
        $this->_orientation = 'L';
    }

    public function getOrientation(): string
    {
        return $this->_orientation;
    }

    /**
     * Will return full local path to created file
     */
    public function generate(?string $filename = null): ?string
    {
        $pdf = new MYPDF(
            $this->getOrientation(),
            $this->_unit
        );
        $pdf->setTitle($this->_title);

        // set margins
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(5);
        $pdf->SetMargins(0.4, 0, 0.6, true);

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);

        // set font
        // $pdf->SetFont('Helvetica', '', 12);
        $pdf->SetFont(self::FONT_NAME, '', 12);

        // add a page
        $pdf->AddPage();

        // view html
        $html = view($this->_viewName, $this->_viewParams)->render();
        $pdf->writeHTML($html, true, false, true, false, '');

        // filename
        if (null === $filename) {
            $filename = $this->getDocName();
        }

        // output
        $pdfAsString = $pdf->Output('', 'S');
        $fullSavePath = "public/pdf/$filename";
        $retPut = Storage::disk('local')->put(
            $fullSavePath,
            $pdfAsString
        );
        return ($retPut === true) ? Storage::disk('local')->path($fullSavePath): null;
    }

    private function getDocName(): string
    {
        return md5(
            date('YmdHis') . rand(1, 2000)
        ) . '.pdf';
    }
}

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
    // Page header
    /*
    public function Header() {
        // Logo
        $image_file = K_PATH_IMAGES.'logo_example.jpg';
        $this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
        $this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }
    */

    // Page footer
    public function Footer() {
        // Position at Xcm from bottom
        $this->SetY(-1.2);

        // Set font
        $this->SetFont(Pdf::FONT_NAME, '', 10);

        // footer vars
        $isPortrait = $this->CurOrientation == 'P';
        $firstColWid = ($isPortrait) ? '90.5%': '80%';
        $secondColWid = ($isPortrait) ? '18%': '26%';

        // ciclo footer
        $pageStr = 'Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages();
        $html = <<<HTML
            <hr style="width:100%; height:3px; color:#FFF500;" />
            <br />
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="$firstColWid">
                        <b>www.ciclocomunicacao.com.br</b>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        Rua Helena Steimberg, 1456 - Jd. São Carlos - Campinas/SP
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        (19) 3213.3871
                    </td>
                    <td width="$secondColWid" align="right">
                        $pageStr
                    </td>
                </tr>
            </table>
        HTML;
        $this->writeHTML($html);
    }
}