#!/usr/bin/env python2

import sys
from time import sleep
from datetime import datetime
import os


path = os.path.dirname(os.path.abspath(__file__))+"/max7219/sevensegment/"
sys.path.append(path)

from sevenSegment import SevenSegment

segment = SevenSegment()

while True:
  now = datetime.now()
  hour = now.hour
  minute = now.minute
  second = now.second
  dot = second % 2 == 0                # calculate blinking dot
  # Set hours
  segment.writeDigit(4, int(hour / 10))     # Tens
  segment.writeDigit(3, hour % 10, dot)     # Ones
  # Set minutes
  segment.writeDigit(2, int(minute / 10))   # Tens
  segment.writeDigit(1, minute % 10)        # Ones
  sleep(1)
