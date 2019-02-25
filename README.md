<h2 align="center">
   Dcompose
</h2>

<p align="center">
    <a href="https://packagist.org/packages/percymamedy/dcompose"><img src="https://poser.pugx.org/percymamedy/dcompose/v/stable" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/percymamedy/dcompose"><img src="https://poser.pugx.org/percymamedy/dcompose/v/unstable" alt="Latest Unstable Version"></a>
    <a href="https://packagist.org/packages/percymamedy/dcompose"><img src="https://poser.pugx.org/percymamedy/dcompose/license" alt="License"></a>
    <a href="https://packagist.org/packages/percymamedy/dcompose"><img src="https://poser.pugx.org/percymamedy/dcompose/downloads" alt="Total Downloads"></a>
</p>

## Introduction
[Laradock](https://laradock.io/) is an awesome tool that helps with building a [Docker](https://www.docker.com/) environment for running 
your Laravel or PHP apps.

However, it can be quite a pain to identify and use only specific components needed for your app. Most of the time
you'll find yourself using only few images and remembering which one (So you can run docker-compose up -d <service>..) 
for which project is a pain.

I built Dcompose because I was always finding myself copying the images' Docker files needed from laradock to
my projects and recreating the docker-compose.yml file everytime for each project. Inspired 
by [Composer](https://getcomposer.org/), I embarked on a quest to build my own tool that would 
"require" in my projects only the components that I needed and update automatically my docker-compose.yml file.

This is how Dcompose was born. I do not know where this library will end up but my hope is for people who've
had the same issue as me might find it useful.

## License
Dcompose is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

### Installation
Fisrt make sur you have Docker and Docker Compose installed on your system. Check out [Docker](https://docs.docker.com/install/)
and [Docker Compose](https://docs.docker.com/compose/install/) installation docs.

Then, download Dcompose using Composer:

```bash
$ composer global require percymamedy/dcompose
```

Make sure to place composer's system-wide vendor bin directory in your $PATH so the Dcompose executable 
can be located by your system. This directory exists in different locations based on your operating system; 
however, some common locations include:

- macOS: $HOME/.composer/vendor/bin
- GNU / Linux Distributions: $HOME/.config/composer/vendor/bin

### Usage

#### Init command
With the init command you may start defining which services you want to use in your project. You should
run this command in the root of your project like follows:

```bash
$ dcompose init
```

This is an interacting command which will ask you about your project name and services which you'll use. 
Services are just the Laradock services which exists, check out [laradock's github repo](https://github.com/laradock/laradock) 
to get a sense of all services which you can require.

After running this command a new ```.docker/``` folder will be created into your project's root directory which
will contain only the services and correct ```docker-compose.yml``` file layout.

You will also find a ```.env``` file inside the .docker folder. You can modify this file to change values
specific for your environment.

Now you may run the following command inside the ```.docker/``` folder to run your project :

```bash
$ docker-compose up -d
```

#### Require command
If you've missed a service or you need another service from laradock you can run the require command as 
follows:

```bash
$ dcompose require <service_name>
```

Where ```<service_name>``` is the name of one of laradock's services. This will then add the service to 
your ```.docker/``` folder and ```docker-compose.yml``` file.

Now you may run the following command inside the ```.docker/``` folder :

```bash
$ docker-compose up -d
```

or :

```bash
$ ./containers start
```

If you've ran the ```generate-tools``` command.

#### Remove command
You can remove a service using the following command :

```bash
$ dcompose remove <service_name>
```

This is not going to remove the service folder from the ```.docker/``` folder and correct 
sections from ```docker-compose.yml``` file. 

However the container, image and volume created for this service will still be on your system. To
remove them completely you should run docker commands :

```bash
$ docker rm <container_name>
``` 

```bash
$ docker rmi <image_name>
```

```bash
$ docker volume rm <volume_name>
```

#### Generate tools command
Generating the commands line tools helps in running and executing docker containers from
laradock :

```bash
$ dcompose generate-tools
```

A new ```containers``` script will be created which gives you access to three options:

Start docker containers:
```bash
$ ./containers start
```

Stop docker containers:
```bash
$ ./containers stop
```

Ssh into workspace container after starting contaners :
```bash
$ ./containers workspace
```

#### Refresh command
laradock files are fetched and cached such that ```require```, ```remove``` and ```init``` command 
are carried out faster without the need to refetch files from dist all the time.

However, there is not way of knowing when laradock has been updated, thus the ```refresh``` command
allows us to refresh the cached laradock files :

```bash
$ dcompose refresh
```
 
### Road map v0.2
- [ ] Make code more consistent with an object oriented approach.
- [ ] Use something like doctrine cache for managing laradock file hence eliminating the need 
for refresh command by having expiration.

### Road map v0.3
- [ ] Add a manifest file in JSON or YML that will contain all docker containers hence eliminating the
need for commiting the .docker folder.

### Credits
Big Thanks to all developers who worked hard to create something amazing!

### Creator
[![Percy Mamedy](https://img.shields.io/badge/Author-Percy%20Mamedy-orange.svg)](https://twitter.com/PercyMamedy)

Twitter: [@PercyMamedy](https://twitter.com/PercyMamedy)
<br/>
GitHub: [percymamedy](https://github.com/percymamedy)
