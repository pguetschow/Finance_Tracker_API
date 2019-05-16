<?php
declare(strict_types=1);


namespace App\Presenter;

use App\Doctrine\Repository\CategoryRepository;
use App\Model\Entity\Category;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class CategoryPresenter
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


    /**
     * @return Category[]
     */
    public function listCategories(): array
    {

        $data = $this->categoryRepository->findAll();
        $categories = [];
        foreach ($data as $category) {
            $categories[] = new Category($category->getId(), $category->getName());
        }

        return $categories;
    }

}
