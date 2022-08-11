<?php

namespace AbdiZbn\SimpleAuditLog;


/**
 * This trait allows you to keep history of the changes in the Model.
 * Available on created, updating and deleted event. You can enable or
 * disable auditing and choose simply events with the audits config file.
 *
 * Trait AuditableTrait
 *
 * @package App\Traits
 */
trait AuditableTrait
{
    /**
     * @var string
     */
    private $event;

    /**
     * Get dontAuditing variable.
     *
     * @return array
     */
    public function getDontAuditing()
    {
        return [];
    }

    /**
     * Get doAuditing variable.
     *
     * @return array
     */
    public function getDoAuditing()
    {
        return [];
    }

    /**
     * Determine if the given key may be auditable.
     *
     * @param $key
     *
     * @return bool
     */
    public function isAuditable($key)
    {
        if (in_array($key, $this->getDontAuditing())
        ) {
            return false;
        }

        if (in_array($key, $this->getDoAuditing()) ||
            count($this->getDoAuditing()) === 0
        ) {
            return true;
        }

        return false;
    }

    /**
     * Get module.
     *
     * @return mixed
     */
    abstract function getModule();

    /**
     * Relation with Entity
     *
     * @param int $limit
     * @param string $order
     *
     * @return mixed
     */
    public function audits($limit = 100, $order = 'desc')
    {
        return $this->hasMany(AuditLog::class, 'module')
            ->where('module', $this->getModule())
            ->orderBy('created_at', $order)
            ->limit($limit);
    }

    /**
    * Create the event listeners for the boot events.
    */
    protected static function bootAuditableTrait()
    {
        if (self::isEnablingAudit()) {

            static::created(function ($model) {
                $model->auditCreated();
            });

            static::updating(function ($model) {
                $model->auditUpdating();
            });

            static::deleted(function ($model) {
                $model->auditDeleted();
            });
        }
    }

    /**
     * Set audit event.
     *
     * @param $event
     */
    protected function setAuditEvent($event)
    {
        $this->event = $event;
    }

    /**
     * Get audit event.
     *
     * @return mixed
     */
    protected function getAuditEvent()
    {
        return $this->event;
    }

    /**
     * Keep changes on created event.
     */
    public function auditCreated()
    {
        $this->setAuditEvent('created');

        if ($this->isEventAuditable($this->getAuditEvent())) {
            $new = $this->getdata($this->attributes);

            $this->toAudit([], $new);
        }
    }

    /**
     * Keep changes on updating event.
     */
    public function auditUpdating()
    {
        $this->setAuditEvent('updating');

        if ($this->isEventAuditable($this->getAuditEvent())) {
            $old = $this->getdata($this->original);
            $new = $this->getdata($this->attributes);

            $this->toAudit($old, $new);
        }
    }

    /**
     * Keep changes on deleted event.
     */
    public function auditDeleted()
    {
        $this->setAuditEvent('deleted');

        if ($this->isEventAuditable($this->getAuditEvent())) {
            $old = $this->getdata($this->original);

            $this->toAudit($old, []);
        }
    }

    /**
     * Check event enable
     *
     * @param $event
     *
     * @return bool
     */
    protected static function isEventAuditable($event): bool
    {
        $auditableEvent = config('audit.events');

        if (in_array($event, $auditableEvent)) {
            return true;
        }

        return false;
    }

    /**
     * Return the auditable data.
     *
     * @param $data
     *
     * @return mixed
     */
    protected function getData($data)
    {
        $result = [];

        foreach ($data as $key => $value) {
            if ($this->isAuditable($key)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Get status of audit enabling.
     *
     * @return mixed
     */
    public static function isEnablingAudit()
    {
        return config('audit.enabled', true);
    }

    /**
     * Save data on the audit table with the given values.
     *
     * @param $old
     * @param $new
     */
    private function toAudit($old, $new)
    {
        // prevent saving zero changes.
        if ( count($old) > 0 || count($new) > 0 ) {
            /** @var AuditLog $audit */
            $audit = new AuditLog();

            $audit->user_id = $this->getUserId();
            $audit->event = $this->getAuditEvent();
            $audit->old_values = json_encode($old);
            $audit->new_values = json_encode($new);
            $audit->module = $this->getModule();
            $audit->module_id = $this->getModule();
            $audit->ip = $this->getUserIp();
            $audit->user_agent = $this->getUserAgent();

            $audit->save();
        }
    }

    /**
     * Get user ip.
     *
     * @return string|null
     */
    private function getUserIp()
    {
        return ClientData::real_ip();
    }

    /**
     * Get user agent.
     *
     * @return mixed|null
     */
    private function getUserAgent()
    {
        return ClientData::user_agent() ? ClientData::user_agent() : null;
    }

    /**
     * Get auth user id.
     *
     * @return int
     */
    private function getUserId()
    {
        if (app('auth')->check()) {
            return app('auth')->id();
        }

        return 0;
    }

    /**
     * Get module id.
     *
     * @return int
     */
    private function getModuleId()
    {
        if ($this->id) {
            return $this->id;
        }

        return 0;
    }
}
