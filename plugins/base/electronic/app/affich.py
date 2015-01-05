#!/usr/bin/env python2

from time import sleep
import sys

path = "max7219/sevensegment/"
sys.path.append(path)
from sevenSegment import SevenSegment

segment = SevenSegment()

def _all():
 for x in range(256):
  print str(x)+" : "+str(bin(x))
  segment.writeDigitRaw(1, x)
  sys.stdin.read(1)

def _hex():
 for x in range(16):
  segment.writeDigit(1, x)
  sleep(1)

def _affe():
 segment.writeDigit(8, 13)
 segment.writeDigit(7, 2)
 segment.writeDigit(6, 3)
 segment.writeDigit(5, 4)
 segment.writeDigit(4, 5)
 segment.writeDigit(3, 6)
 segment.writeDigit(2, 7)
 segment.writeDigit(1, 8)

# _all()
# _hex()
# _affe()


# pour un E : 01001111
# 
# Test
# segment.writeDigitRaw(8, 0b00111100)
# segment.writeDigitRaw(7, 0b01001111)
# segment.writeDigitRaw(6, 0b01000110)
# segment.writeDigitRaw(5, 0b01110111)
# segment.writeDigitRaw(4, 0b00000110)
# segment.writeDigitRaw(3, 0b01110110)
# segment.writeDigitRaw(2, 0b01001111)
# segment.writeDigitRaw(1, 0b10000000)

def RepresentsInt(s):
    try: 
        int(s)
        return True
    except ValueError:
        return False

# ordre des digits de droite a gauche
# milieu        1
# haut gauche   0
# bas gauche    1
# bas           1
# bas droite    0
# haut droite   1
# haut          1
# point         0
# 01101101
letter_list = {
    " " : 0b00000000,
    "-" : 0b00000001,
    "A" : 0b01110111,
    "B" : 0b00011111,
    "C" : 0b01001110,
    "D" : 0b00111101,
    "E" : 0b01001111,
    "F" : 0b01000111,
    "G" : 0b01011111,
    "H" : 0b00110111,
    "I" : 0b00000110,
    "J" : 0b00111100,
    "L" : 0b00001110,
    "M" : 0b01110110,
    "N" : 0b00010101,
    "O" : 0b01111110,
    "P" : 0b01100111,
    "S" : 0b01011011,
    "T" : 0b00001111,
    "U" : 0b00111110,
    "V" : 0b00011100,
    "Y" : 0b00100111,
    "Z" : 0b01101101,
}

argv = sys.argv

if(len(argv)>1):
    case = 8

    lettres = []

    for (i, item) in enumerate(argv):
        if(i!= 0):
            for (j, char) in enumerate(item):
                if(j-1 >= 0 and item[j-1] == "."):
                        lettres.append(str(item[j-1])+str(item[j]))

                else:        
                    lettres.append(str(item[j]))

    print lettres


    for (i, item) in enumerate(lettres):
        point = False

        if(len(lettres[i])>1 and "." in lettres[i]):
            lettres[i] = lettres[i][1]
            point = True

        if( RepresentsInt(lettres[i]) and len(lettres[i]) == 1 and case <= 8 and case > 0):
            segment.writeDigit(int(case), int(lettres[i]), point )
            case-=1

        if( isinstance(lettres[i], str) and len(lettres[i]) == 1 and case <= 8 and case > 0 and lettres[i].upper() in letter_list):
            if(point):
                letter_list[lettres[i].upper()]+=0b10000000

            segment.writeDigitRaw(int(case), letter_list[lettres[i].upper()] )
            case-=1