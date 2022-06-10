<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  (C) 2022 Open Source Matters, Inc. <https://www.joomla.org>
 * @license        GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CMS\Event;

use Joomla\CMS\Event\Model\BeforeBatchEvent;
use Joomla\CMS\Event\QuickIcon\GetIconEvent;
use Joomla\CMS\Event\Table\AfterBindEvent;
use Joomla\CMS\Event\Table\AfterCheckinEvent;
use Joomla\CMS\Event\Table\AfterCheckoutEvent;
use Joomla\CMS\Event\Table\AfterDeleteEvent;
use Joomla\CMS\Event\Table\AfterHitEvent;
use Joomla\CMS\Event\Table\AfterLoadEvent;
use Joomla\CMS\Event\Table\AfterMoveEvent;
use Joomla\CMS\Event\Table\AfterPublishEvent;
use Joomla\CMS\Event\Table\AfterReorderEvent;
use Joomla\CMS\Event\Table\AfterResetEvent;
use Joomla\CMS\Event\Table\AfterStoreEvent;
use Joomla\CMS\Event\Table\BeforeBindEvent;
use Joomla\CMS\Event\Table\BeforeCheckinEvent;
use Joomla\CMS\Event\Table\BeforeCheckoutEvent;
use Joomla\CMS\Event\Table\BeforeDeleteEvent;
use Joomla\CMS\Event\Table\BeforeHitEvent;
use Joomla\CMS\Event\Table\BeforeLoadEvent;
use Joomla\CMS\Event\Table\BeforeMoveEvent;
use Joomla\CMS\Event\Table\BeforePublishEvent;
use Joomla\CMS\Event\Table\BeforeReorderEvent;
use Joomla\CMS\Event\Table\BeforeResetEvent;
use Joomla\CMS\Event\Table\BeforeStoreEvent;
use Joomla\CMS\Event\Table\CheckEvent;
use Joomla\CMS\Event\Table\ObjectCreateEvent;
use Joomla\CMS\Event\Table\SetNewTagsEvent;
use Joomla\CMS\Event\View\DisplayEvent;
use Joomla\CMS\Event\WebAsset\WebAssetRegistryAssetChanged;
use Joomla\CMS\Event\Workflow\WorkflowFunctionalityUsedEvent;
use Joomla\CMS\Event\Workflow\WorkflowTransitionEvent;
use Joomla\Event\Event;

\defined('JPATH_PLATFORM') or die;

/**
 * Returns the most suitable event class for a Joomla core event name
 *
 * @since 4.2.0
 */
trait CoreEventAware
{
	/**
	 * Maps event names to concrete Event classes.
	 *
	 * This is only for events with invariable names. Events with variable names are handled with
	 * PHP logic in the getEventClassByEventName class.
	 *
	 * @var   array
	 * @since 4.2.0
	 */
	private static $eventNameToConcreteClass = [
		// Model
		'onBeforeBatch'               => BeforeBatchEvent::class,
		// Quickicon
		'onGetIcon'                   => GetIconEvent::class,
		// Table
		'onTableAfterBind'            => AfterBindEvent::class,
		'onTableAfterCheckin'         => AfterCheckinEvent::class,
		'onTableAfterCheckout'        => AfterCheckoutEvent::class,
		'onTableAfterDelete'          => AfterDeleteEvent::class,
		'onTableAfterHit'             => AfterHitEvent::class,
		'onTableAfterLoad'            => AfterLoadEvent::class,
		'onTableAfterMove'            => AfterMoveEvent::class,
		'onTableAfterPublish'         => AfterPublishEvent::class,
		'onTableAfterReorder'         => AfterReorderEvent::class,
		'onTableAfterReset'           => AfterResetEvent::class,
		'onTableAfterStore'           => AfterStoreEvent::class,
		'onTableBeforeBind'           => BeforeBindEvent::class,
		'onTableBeforeCheckin'        => BeforeCheckinEvent::class,
		'onTableBeforeCheckout'       => BeforeCheckoutEvent::class,
		'onTableBeforeDelete'         => BeforeDeleteEvent::class,
		'onTableBeforeHit'            => BeforeHitEvent::class,
		'onTableBeforeLoad'           => BeforeLoadEvent::class,
		'onTableBeforeMove'           => BeforeMoveEvent::class,
		'onTableBeforePublish'        => BeforePublishEvent::class,
		'onTableBeforeReorder'        => BeforeReorderEvent::class,
		'onTableBeforeReset'          => BeforeResetEvent::class,
		'onTableBeforeStore'          => BeforeStoreEvent::class,
		'onTableCheck'                => CheckEvent::class,
		'onTableObjectCreate'         => ObjectCreateEvent::class,
		'onTableSetNewTags'           => SetNewTagsEvent::class,
		// View
		'onBeforeDisplay'             => DisplayEvent::class,
		'onAfterDisplay'              => DisplayEvent::class,
		// Worflow
		'onWorkflowFunctionalityUsed' => WorkflowFunctionalityUsedEvent::class,
		'onWorkflowAfterTransition'   => WorkflowTransitionEvent::class,
		'onWorkflowBeforeTransition'  => WorkflowTransitionEvent::class,
	];

	/**
	 * Get the concrete event class name for the given event name.
	 *
	 * This method falls back to the generic Joomla\Event\Event class if the event name is unknown
	 * to this trait.
	 *
	 * @param   string  $eventName  The event name
	 *
	 * @return  string The event class name
	 * @since 4.2.0
	 */
	protected static function getEventClassByEventName(string $eventName): string
	{
		if (strpos($eventName, 'onWebAssetRegistryChangedAsset') === 0)
		{
			return WebAssetRegistryAssetChanged::class;
		}

		return self::$eventNameToConcreteClass[$eventName] ?? Event::class;
	}
}
