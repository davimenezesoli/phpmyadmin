<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Server;

use PhpMyAdmin\Config\PageSettings;
use PhpMyAdmin\Controllers\AbstractController;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\SqlQueryForm;
use PhpMyAdmin\Template;
use PhpMyAdmin\Url;

/**
 * Server SQL executor
 */
class SqlController extends AbstractController
{
    /** @var SqlQueryForm */
    private $sqlQueryForm;

    /** @var DatabaseInterface */
    private $dbi;

    public function __construct(
        ResponseRenderer $response,
        Template $template,
        SqlQueryForm $sqlQueryForm,
        DatabaseInterface $dbi
    ) {
        parent::__construct($response, $template);
        $this->sqlQueryForm = $sqlQueryForm;
        $this->dbi = $dbi;
    }

    public function __invoke(): void
    {
        $GLOBALS['errorUrl'] = $GLOBALS['errorUrl'] ?? null;

        $this->addScriptFiles([
            'makegrid.js',
            'vendor/jquery/jquery.uitablefilter.js',
            'vendor/stickyfill.min.js',
            'sql.js',
        ]);

        $pageSettings = new PageSettings('Sql');
        $this->response->addHTML($pageSettings->getErrorHTML());
        $this->response->addHTML($pageSettings->getHTML());
        $GLOBALS['errorUrl'] = Url::getFromRoute('/');

        if ($this->dbi->isSuperUser()) {
            $this->dbi->selectDb('mysql');
        }

        $this->response->addHTML($this->sqlQueryForm->getHtml('', ''));
    }
}
