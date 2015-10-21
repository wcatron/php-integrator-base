config  = require './config.coffee'
Service = require './service.coffee'

module.exports =
    ###*
     * Configuration settings.
    ###
    config:
        phpCommand:
            title       : 'PHP command'
            description : 'The path to your PHP binary (e.g. /usr/bin/php, php, ...).'
            type        : 'string'
            default     : 'php'
            order       : 1

        composerCommand:
            title       : 'Composer command'
            description : 'The path to your Composer binary (e.g.: /usr/bin/composer, composer.phar, composer, ...).'
            type        : 'string'
            default     : 'composer'
            order       : 2

        autoloadScripts:
            title       : 'Path to autoloading script'
            description : 'The relative path to your autoloading script (usually autoload.php generated by composer).
                           Multiple comma-separated paths are supported, which will be tried in the specified order,
                           which is useful if you use different paths for different projects.'
            type        : 'array'
            default     : ['autoload.php', 'vendor/autoload.php']
            order       : 3

        classMapScripts:
            title       : 'Path to classmap script'
            description : 'The relative path to your class map (usually autoload_classmap.php generated by composer).
                           Multiple comma-separated paths are supported, which will be tried in the specified order,
                           which is useful if you use different paths for different projects.'
            type        : 'array'
            default     : ['vendor/composer/autoload_classmap.php', 'autoload/ezp_kernel.php']
            order       : 4

        insertNewlinesForUseStatements:
            title       : 'Insert newlines for use statements'
            description : 'When enabled, the plugin will add additional newlines before or after an automatically added
                           use statement when it can\'t add them nicely to an existing group. This results in more
                           cleanly separated use statements but will create additional vertical whitespace.'
            type        : 'boolean'
            default     : false
            order       : 5

    ###*
     * The exposed service.
    ###
    service: null

    ###*
     * Activates the package.
    ###
    activate: ->
        # See also pull request #197 - Disabled for now because it does not allow the user to reactivate or try again.
        # return unless config.testConfig()


        # TODO: Config has to become a class, dependency inject.

        config.init()

        @service = new Service()
        @service.activate()

    ###*
     * Deactivates the package.
    ###
    deactivate: ->
        @service.deactivate()

    ###*
     * Retrieves the service exposed by this package.
    ###
    getService: ->
        return @service
