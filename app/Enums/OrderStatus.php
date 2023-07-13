<?php

namespace App\Enums;

abstract class OrderStatus
{
    const Pending = 10;
    const Preparing = 20;
    const Delivering = 30;
    const PartlyDelivered = 40;
    const Delivered = 100;
}
