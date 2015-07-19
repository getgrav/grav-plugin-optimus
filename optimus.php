<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

class OptimusPlugin extends Plugin
{
    protected $optimus;

    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
        ];
    }

    /**
     * Activate plugin if path matches to the configured one.
     */
    public function onPluginsInitialized()
    {
        if ($this->isAdmin()) {
            $this->active = false;
            return;
        }

        require_once __DIR__ . '/classes/optimus.php';

        $this->optimus = new Optimus($this->config->get('plugins.optimus.license_key'));

        $this->enable([
            'onImageMediumSaved' => ['onImageMediumSaved', 0],
        ]);
    }


    public function onImageMediumSaved(Event $event)
    {
        $path = $event['image'];
        $result = $this->optimus->optimize($path);

        if (!empty($result)) {
            file_put_contents($path, $result);
        }
    }
}
