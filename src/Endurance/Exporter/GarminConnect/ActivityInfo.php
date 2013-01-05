<?php 

namespace Endurance\Exporter\GarminConnect;

class ActivityInfo
{
    protected $id;
    protected $startTimeLocal;
    protected $startTimeGMT;

    public function __construct(array $info)
    {
        $this->id = $info['activityId'];
        $this->startTimeLocal = new \DateTime($info['startTimeLocal']);
        $this->startTimeGMT = new \DateTime($info['startTimeGMT']);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getStartTimeLocal()
    {
        return $this->startTimeLocal;
    }

    public function getStartTimeGMT()
    {
        return $this->startTimeGMT;
    }
}
