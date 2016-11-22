<?php

namespace LArtie\TelegramBotPagination\Tests;

use LArtie\TelegramBotPagination\Core\CallbackQueryPagination;

/**
 * Class CallbackQueryPaginationTest
 */
final class CallbackQueryPaginationTest extends \TestCase
{
    /**
     * @var int
     */
    private $limit = 5;

    /**
     * @var int
     */
    private $selectedPage;

    /**
     * @var string
     */
    private $command;

    /**
     * @var array
     */
    private $items;

    /**
     * CallbackQueryPaginationTest constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->items = range(1, 100);
        $this->command = 'testCommand';
        $this->selectedPage = random_int(1, 15);
    }

    public function test_valid_constructor()
    {
        $cbq = new CallbackQueryPagination($this->items, $this->command, $this->selectedPage, $this->limit);

        $data = $cbq->paginate();

        $this->assertCount($this->limit, $data['items']);
        $this->assertArrayHasKey('keyboard', $data);
        $this->assertArrayHasKey(0, $data['keyboard']);
        $this->assertArrayHasKey('text', $data['keyboard'][0]);
        $this->assertStringStartsWith($this->command, $data['keyboard'][0]['callback_data']);
    }

    /**
     * @expectedException \LArtie\TelegramBotPagination\Exceptions\CallbackQueryPaginationException
     */
    public function test_invalid_constructor()
    {
        $cbq = new CallbackQueryPagination($this->items, $this->command, 10000, $this->limit);
        $cbq->paginate();
    }

    public function test_valid_paginate()
    {
        $cbq = new CallbackQueryPagination($this->items, $this->command, $this->selectedPage, $this->limit);

        $length = (int)ceil(count($this->items)/$this->limit);

        for ($i = 1; $i < $length; $i++) {
            $cbq->paginate($i);
        }
    }

    /**
     * @expectedException \LArtie\TelegramBotPagination\Exceptions\CallbackQueryPaginationException
     */
    public function test_invalid_paginate()
    {
        $cbq = new CallbackQueryPagination($this->items, $this->command, $this->selectedPage, $this->limit);

        $length = (int)ceil(count($this->items)/$this->limit) + 1;

        for ($i = $length; $i < $length * 2; $i++) {
            $cbq->paginate($i);
        }
    }

    /**
     * @expectedException \LArtie\TelegramBotPagination\Exceptions\CallbackQueryPaginationException
     */
    public function test_invalid_max_buttons()
    {
        $cbq = new CallbackQueryPagination(range(1, 240));
        $cbq->setMaxButtons(2);
        $cbq->paginate();
    }

    /**
     * @expectedException \LArtie\TelegramBotPagination\Exceptions\CallbackQueryPaginationException
     */
    public function test_invalid_selected_page()
    {
        $cbq = new CallbackQueryPagination(range(1, 240));
        $cbq->setSelectedPage(-5);
        $cbq->paginate();
    }

    /**
     * @expectedException \LArtie\TelegramBotPagination\Exceptions\CallbackQueryPaginationException
     */
    public function test_invalid_wrap_selected_button()
    {
        $cbq = new CallbackQueryPagination(range(1, 240));
        $cbq->setWrapSelectedButton('$sdlfk$');
        $cbq->paginate();
    }
}
