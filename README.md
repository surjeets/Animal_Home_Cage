# Animal Home Cage Monitoring System with GPIO Controls #

The animal home-cage monitoring system is a raspberryPi based solution for automated behavior
monitoring of rodents. This system uses a picam (prefereably IR) to record video in the home-cage.

## Install ##

- Step 1: Install Raspbian on your RPi

- Step 2: Attach camera to RPi and enable camera support (http://www.raspberrypi.org/camera)

- Step 3: Update your RPi with the following commands:

'''
sudo apt-get update

sudo apt-get dist-upgrade

'''

- Step 4: Clone the code from github and run the install script with the following commands:

''' 
git clone https://github.com/surjeets/Animal_Home_Cage.git

cd Animal_Home_Cage

./install.sh

'''
## Commands ##

To stop the interface run:

'''
cd Animal_Home_Cage

./stop.sh

'''
To remove the interface run:

cd Animal_Home_Cage

./remove.sh
'''