<?php

namespace LArtie\TelegramBotPagination\Contract;

use LArtie\TelegramBotPagination\Core\CallbackQueryPagination;

/**
 * Interface CallbackQueryPaginationContract
 * @package LArtie\TelegramBotPagination
 */
interface CallbackQueryPaginationContract
{
    /**
     * @param int $maxButtons
     * @return CallbackQueryPagination
     */
    public function setMaxButtons(int $maxButtons = 5) : CallbackQueryPagination;

    /**
     * >#VALUE#<, <#VALUE#>, |#VALUE#| etc...
     *
     * @param string $wrapSelectedButton
     * @return CallbackQueryPagination
     */
    public function setWrapSelectedButton(string $wrapSelectedButton = '« #VALUE# »') : CallbackQueryPagination;

    /**
     * @param string $command
     * @return CallbackQueryPagination
     */
    public function setCommand(string $command = 'pagination'): CallbackQueryPagination;

    /**
     * @param int $selectedPage
     * @return CallbackQueryPagination
     */
    public function setSelectedPage(int $selectedPage): CallbackQueryPagination;

    /**
     * CallbackQueryPaginationContract constructor.
     *
     * @param array $items
     * @param string $command
     * @param int $selectedPage
     * @param int $limit
     */
    public function __construct(array $items, string $command, int $selectedPage, int $limit);

    /**
     * @param int $selectedPage
     * @return array
     */
    public function paginate(int $selectedPage = null) : array;
}