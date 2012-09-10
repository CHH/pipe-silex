#= require jquery.js

class Application
    constructor: ->
        console.log "Setup"

    run: ->
        $("body > h1").fadeOut().fadeIn()

app = new Application

$(document).ready ->
    app.run()

