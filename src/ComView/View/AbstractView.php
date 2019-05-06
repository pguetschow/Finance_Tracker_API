<?php
declare(strict_types=1);


namespace App\ComView\View;

use Eos\ComView\Server\Model\Value\ViewRequest;
use Eos\ComView\Server\Model\Value\ViewResponse;
use Eos\ComView\Server\View\ViewInterface;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
abstract class AbstractView implements ViewInterface
{
    /**
     * @param ViewRequest $request
     * @param array|null $data
     * @return ViewResponse
     */
    protected function createResponse(ViewRequest $request, ?array $data): ViewResponse
    {
        return new ViewResponse($request->getParameters(), $request->getPagination(), $data, $request->getOrderBy());
    }
}
