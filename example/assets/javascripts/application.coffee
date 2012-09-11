#= require jquery

class Application
    constructor: ->
        console.log "Setup"

    run: ->
        $("body > h1").fadeOut("fast").fadeIn("fast")

app = new Application

$ -> app.run()

