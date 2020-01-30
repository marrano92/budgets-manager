<?php

namespace App\Repository;


use App\Expense;

/**
 * Interface ExpenseRepositoryInterface
 * @package App\Repository
 */
interface ExpenseRepositoryInterface
{
    public function find($id): self;

    public function save(Expense $expense);

    public function remove(Expense $expense);
}
