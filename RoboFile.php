<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{

    private function _sources() {
        $sources = [
            // DKAN Open Data Stack Source
            "git@github.com:OpenDataStack/dkan-opendatastack.git" => "src/dkan-opendatastack"
        ];
        return $sources;
    }

    public function setup()
    {
        $this->io()->section("Set up project for development");
        $this->_mkdir('src');

        foreach ($this->_sources() as $repo => $destination) {
            // Clone 
            $this->taskGitStack()
            ->stopOnFail()
            ->cloneRepo($repo, $destination)
            ->run();
        }
    }

    public function gitPull()
    {
        $this->io()->section("Update all repositories");

        foreach ($this->_sources() + ['repo' => '.'] as $repo => $destination) {
            $this->taskGitStack()
            ->dir($destination)
            ->stopOnFail()
            ->pull()
            ->run();
        }
    }

    public function gitPush()
    {
        $this->io()->section("Push all repositories");

        foreach ($this->_sources() + ['repo' => '.'] as $repo => $destination) {
            $this->taskGitStack()
            ->dir($destination)
            ->stopOnFail()
            ->push()
            ->run();
        }
    }

    public function gitStatus()
    {
        $this->io()->section("Status of all repositories");

        foreach ($this->_sources() + ['repo' => '.'] as $repo => $destination) {
            $this->taskExec("git status")
            ->dir($destination)
            ->run();
        }
    }

    public function dockerUpProd()
    {
        $this->taskExec('docker-compose')->arg('stop')->run();
        $this->taskExec('docker-compose')->arg('up')->run();
    }

    public function dockerUpDev()
    {
        $dockerCompose = [
            'docker-compose',
            '-f docker-compose.yml',
            '-f docker-compose.dev.yml'
        ];

        $this->taskExec('docker-compose')->arg('stop')->run();
        $this->taskExec(implode(' ', $dockerCompose))->args('up', '-d')->run();
    }

    public function dockerBuild()
    {
        $this->taskExec('docker-compose')->arg('stop')->run();
        $this->taskExec('docker-compose')
        ->arg('up')
        ->option('build')
        ->run();
    }

    public function dockerPush()
    {
        $localTag = 'dkan-opendatastack';
        $remoteTag = 'opendatastack/' . $localTag;

        $this->taskExec('docker')
        ->arg('build -t ' . $localTag . ' ' . '.')
        ->run();

        $this->taskExec('docker')
        ->arg('tag ' . $localTag . ' ' . $remoteTag)
        ->run();

        $this->taskExec('docker')
        ->arg('push ' . $remoteTag)
        ->run();
    }

    public function dkanInstall()
    {
        $this->taskExec('
            time docker-compose exec --user=www-data dkan_apache_php /bin/bash -c "cd /var/www/html/docroot && drush si dkan --verbose --account-pass=\'admin\' --site-name=\'DKAN\' install_configure_form.update_status_module=\'array(FALSE,FALSE)\' --yes"
        ')->run();
    }

    public function dkanDrush(array $args)
    {
        $this->taskExec('
            docker-compose exec --user=www-data dkan_apache_php /bin/bash -c "cd /var/www/html/docroot && drush ' . implode(' ', $args) . '"
        ')->run();
    }
}

