<?php

namespace LArtie\TelegramBotPagination\Core;

use Illuminate\Support\Facades\Validator;
use LArtie\TelegramBotPagination\Contract\CallbackQueryPaginationContract;
use LArtie\TelegramBotPagination\Exceptions\CallbackQueryPaginationException;

/**
 * Class CallbackQueryPagination
 * @package LArtie\TelegramBotPagination
 */
class CallbackQueryPagination implements CallbackQueryPaginationContract
{
    /**
     * @var integer
     */
    private $limit;

    /**
     * @var integer
     */
    private $maxButtons = 5;

    /**
     * @var integer
     */
    private $firstPage = 1;

    /**
     * @var integer
     */
    private $selectedPage;

    /**
     * @var integer
     */
    private $numberOfPages;

    /**
     * @var array
     */
    private $items;

    /**
     * @var string
     */
    private $command;

    /**
     * @var string
     */
    private $wrapSelectedButton = '« #VALUE# »';

    /**
     * @inheritdoc
     * @throws CallbackQueryPaginationException
     */
    public function setMaxButtons(int $maxButtons = 5) : CallbackQueryPagination
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make(compact('maxButtons'), [
            'maxButtons' => 'integer|between:5,8',
        ]);

        if ($validator->fails()) {
            throw new CallbackQueryPaginationException($validator->errors()->first());
        }
        $this->maxButtons = $maxButtons;
        return $this;
    }

    /**
     * @inheritdoc
     * @throws CallbackQueryPaginationException
     */
    public function setWrapSelectedButton(string $wrapSelectedButton = '« #VALUE# »') : CallbackQueryPagination
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make(compact('wrapSelectedButton'), [
            'wrapSelectedButton' => 'regex:/#VALUE#/',
        ]);

        if ($validator->fails()) {
            throw new CallbackQueryPaginationException($validator->errors()->first());
        }
        $this->wrapSelectedButton = $wrapSelectedButton;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setCommand(string $command = 'pagination'): CallbackQueryPagination
    {
        $this->command = $command;
        return $this;
    }

    /**
     * @inheritdoc
     * @throws CallbackQueryPaginationException
     */
    public function setSelectedPage(int $selectedPage): CallbackQueryPagination
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make(
            compact('selectedPage'),
            ['selectedPage' => 'integer|between:1,' . $this->numberOfPages],
            ['between' => 'The $selectedPage must be between 1 - $numberOfPages']
        );

        if ($validator->fails()) {
            throw new CallbackQueryPaginationException($validator->errors()->first());
        }
        $this->selectedPage = $selectedPage;
        return $this;
    }

    /**
     * TelegramBotPagination constructor.
     *
     * @inheritdoc
     * @throws CallbackQueryPaginationException
     */
    public function __construct(array $items, string $command = 'pagination', int $selectedPage = 1, int $limit = 3)
    {
        $this->numberOfPages = $this->countTheNumberOfPage(count($items), $limit);

        $this->setSelectedPage($selectedPage);

        $this->items = $items;
        $this->limit = $limit;
        $this->command = $command;
    }

    /**
     * @inheritdoc
     * @throws CallbackQueryPaginationException
     */
    public function paginate(int $selectedPage = null) : array
    {
        if ($selectedPage !== null) {
            $this->setSelectedPage($selectedPage);
        }
        return [
            'items' => $this->getPreparedItems(),
            'keyboard' => $this->generateKeyboard(),
        ];
    }

    /**
     * @return array
     */
    protected function generateKeyboard() : array
    {
        $buttons = [];

        if ($this->numberOfPages > $this->maxButtons) {

            $buttons[] = $this->generateButton($this->firstPage);

            $range = $this->generateRange();

            for ($i = $range['from']; $i < $range['to']; $i++) {
                $buttons[] = $this->generateButton($i);
            }

            $buttons[] = $this->generateButton($this->numberOfPages);

        } else {
            for ($i = 1; $i <= $this->numberOfPages; $i++) {
                $buttons[] = $this->generateButton($i);
            }
        }
        return $buttons;
    }

    /**
     * @return array
     */
    protected function generateRange() : array
    {
        $numberOfIntermediateButtons = $this->maxButtons - 2;

        if ($this->selectedPage == $this->firstPage) {
            $from = 2;
            $to = $from + $numberOfIntermediateButtons;
        } else if ($this->selectedPage == $this->numberOfPages) {
            $from = $this->numberOfPages - $numberOfIntermediateButtons;
            $to = $this->numberOfPages;
        } else {
            if (($this->selectedPage + $numberOfIntermediateButtons) > $this->numberOfPages) {
                $from = $this->numberOfPages - $numberOfIntermediateButtons;
                $to = $this->numberOfPages;
            } else if (($this->selectedPage - 2) < $this->firstPage) {
                $from = $this->selectedPage;
                $to = $this->selectedPage + $numberOfIntermediateButtons;
            } else {
                $from = $this->selectedPage - 1;
                $to = $this->selectedPage + 2;
            }
        }
        return compact('from', 'to');
    }

    /**
     * @param int $nextPage
     * @return array
     */
    protected function generateButton(int $nextPage) : array
    {
        $label = "$nextPage";
        $callbackData = $this->generateCallbackData($nextPage);

        if ($nextPage === $this->selectedPage) {
            $label = str_replace('#VALUE#', $label, $this->wrapSelectedButton);
        }
        return [
            'text' => $label,
            'callback_data' => $callbackData,
        ];
    }

    /**
     * @param int $nextPage
     * @return string
     */
    protected function generateCallbackData(int $nextPage) : string
    {
        return "$this->command?currentPage=$this->selectedPage&nextPage=$nextPage";
    }

    /**
     * @return array
     */
    protected function getPreparedItems() : array
    {
        $offset = $this->getOffset();

        return array_slice($this->items, $offset, $this->limit);
    }

    /**
     * @return int
     */
    protected function getOffset() : int
    {
        return ($this->limit * $this->selectedPage) - $this->limit;
    }

    /**
     * @param $itemsLength
     * @param $limit
     * @return int
     */
    protected function countTheNumberOfPage($itemsLength, $limit) : int
    {
        $numberOfPages = ceil($itemsLength/$limit);

        return (int)$numberOfPages;
    }
}