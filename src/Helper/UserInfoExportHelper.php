<?php
declare(strict_types=1);


namespace App\Helper;

use App\Doctrine\Entity\User;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class UserInfoExportHelper
{

    /**
     * @var \Twig_Environment
     */
    private $render;

    /**
     * @param \Twig_Environment $render
     */
    public function __construct(\Twig_Environment $render)
    {
        $this->render = $render;
    }


    /**
     * @param User $user
     * @return string
     * @throws \Exception
     */
    public function createExportPdf(User $user): string
    {

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdf = new Dompdf($pdfOptions);

        //todo: relevante daten aggregieren

        $html = $this->render->render(
            'mail/gdpr_information.html.twig',
            [
                'user' => $user,
            ]
        );

        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        return $pdf->output();

    }

}
