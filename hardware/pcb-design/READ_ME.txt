regulator:
https://www.digikey.com/product-detail/en/diodes-incorporated/AZ1117H-3.3TRE1/AZ1117H-3.3TRE1DIDKR-ND/5416064

BMS IC:
https://www.arrow.com/en/products/bq24092dgqr/texas-instruments 

R27 : Charging current adjust (1k = 200mA .go through the data sheet for more details)

R2 : do not populate.

note: regulator only power-up the pump(to keep constant flow rate).
 valves and MCU directly power-up by the baterry(they will operate with 3.3v to 5.5v there for no need of regulator) . this will improve the efficiency.
 if you wish to not to use regulator,then do not populate regulator and put 0ohm resistor for R2.


jp1: can be used as main power swithch.else hard wire JP1.
