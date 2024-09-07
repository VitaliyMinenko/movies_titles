<?php
declare(strict_types=1);

namespace App;

use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Serializer as JmsSerializer;

class Serializer
{
    public JmsSerializer $serializer;
    public function __construct()
    {
        $this->serializer = SerializerBuilder::create()->build();
    }
}
