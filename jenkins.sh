#!/bin/bash

mvn clean test
mvn sonar:sonar -P sonar-java
mvn sonar:sonar -P sonar-php
