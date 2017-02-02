#!/usr/bin/env bash
# ./setup/run.sh <functionName>

production.stack() { # ⭐ Run Docker swarm services.
    
    docker stack deploy --compose-file ./setup/container/production.dockerComposeStack.yml
}

production.service() { # TODO: 
    # docker service create --name webappDentristApp --network webappDentrist dentristwebapp:latest
    # docker service create --name webappDentristMysql --network webappDentrist mysql:latest
    # docker service create --name webappDentristPhpmyadmin --network webappDentrist phpmyadmin:latest
}

development() { # ⭐ Run locally either for development or production like testing.
    export DEPLOYMENT=production
    export DEPLOYMENT=development
    docker-compose -f ./setup/container/development.dockerCompose.yml up
}

deployment.buildDistribution() { # ⭐
    # development / production
    export DEPLOYMENT=production
    export DEPLOYMENT=development
    docker-compose -f ./setup/container/deployment.dockerCompose.yml up buildDistributionCode
}

deployment.test() { # ⭐
    docker-compose -f ./setup/container/deployment.dockerCompose.yml up localUnitTest
}

deployment.staging() { # ⭐
    # USAGE: docker-compose -f ./setup/deployment.dockerCompose.yml -f ./setup/development.dockerCompose.yml up --build wordpress localStagingTest
    # USAGE: docker-compose -f ./setup/deployment.dockerCompose.yml up --rm localStagingTest
    docker-compose -f ./setup/container/deployment.dockerCompose.yml -f ./setup/container/development.dockerCompose.yml up wordpress localStagingTest
}

deployment.buildImage() { # ⭐
    # development / production
    export DEPLOYMENT=production
    export DEPLOYMENT=development
    # export COMPOSE_PROJECT_NAME=dentrist # Not needed as name is taken from image field.

    # Problem cannot pass arguments to dockerfile
    docker-compose -f ./setup/container/deployment.dockerCompose.yml build buildImage
    # or 
    docker-compose -f ./setup/container/development.dockerCompose.yml build wordpress

    # Docker CLI implimentation :
    # context is relative to current working directory not like in compose which is relative.
    # docker build --build-arg DEPLOYMENT=${DEPLOYMENT} --tag dentrist-${DEPLOYMENT} -f ./setup/container/wordpress.php5.6.dockerfile ./
}

# Important: call arguments verbatim. i.e. allows first argument to call functions inside file. So that it could be called as "./setup/run.sh <functionName>".
$@
