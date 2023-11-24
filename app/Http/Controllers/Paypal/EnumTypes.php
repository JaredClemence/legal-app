<?php

enum BillingType
{
    case TRIAL;
    case REGULAR;
}
enum BillingIntervalUnit{
    case DAY;
    case WEEK;
    case YEAR;
    case MONTH;
}
enum ProductType
{
    case PHYSICAL;
    case DIGITAL;
    case SERVICE;
}
