<?php declare(strict_types = 1);

namespace App\UI;

use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\Template;

/**
 * Base presenter for all application presenters.
 *
 * @property-read Template $template
 */
abstract class BasePresenter extends Presenter
{

}
