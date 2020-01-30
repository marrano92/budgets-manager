<?php

namespace App\Repository;

use App\Expense;
use stdClass;

/**
 * Class ExpenseRepository
 * @package App\Repository
 */
class ExpenseRepository implements ExpenseRepositoryInterface
{

    /**
     * @var stdClass
     */
    protected $obj;

    public function __construct()
    {
    }

    /**
     * Find Expense by ID
     *
     * @param $id
     *
     * @return ExpenseRepositoryInterface
     */
    public function find($id): ExpenseRepositoryInterface
    {
        $expenses = Expense::where('user_id', '=', $id)->get();
        $total    = 0;

        foreach ($expenses as $expense) {
            $total = $total + $expense->value;
        }

        $this->obj = (object) ['expenses' => $expenses, 'total' => $total];

        return $this;
    }

    public function save(Expense $expense)
    {
        // TODO: Implement save() method.
    }

    public function remove(Expense $expense)
    {
        // TODO: Implement remove() method.
    }

    /**
     * @return stdClass
     */
    public function get_obj()
    {
        return $this->obj;
    }
}
