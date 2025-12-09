<?php

namespace AppKit\Log;

enum LogLevel: int {
    case Error   = 0;
    case Warning = 1;
    case Info    = 2;
    case Debug   = 3;
}
