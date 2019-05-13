<?php
declare(strict_types=1);


namespace App\ComView\View;

use App\Repository\CategoryRepository;
use Eos\ComView\Server\Model\Value\ViewRequest;
use Eos\ComView\Server\Model\Value\ViewResponse;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class ListCategories extends AbstractView
{

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }


    public function createView(string $name, ViewRequest $request): ViewResponse
    {

        $categories = $this->categoryRepository->findAll();
        $data = [];
        foreach ($categories as $category) {
            $data['categories'][] = $category->getName();
        }

        return $this->createResponse($request, $data);
    }


}
