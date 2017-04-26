/*
 *working jquery-1.8.0.min.js
 **/
function JBCountDown(settings) {
    var glob = settings;
   
    function deg(deg) {
        return (Math.PI/180)*deg - (Math.PI/180)*90
    }
    
    /* Vento
    var actual 
    _dias = Math.floor(glob.now / 86400);
    _dias1 = Math.floor(glob.endDate / 86400);
    _horas = 60 - Math.floor(glob.startDate % 86400 / 86400);
    _min = 60 - Math.floor(glob.startDate % 86400 % 3600 / 60);
    _segs = 60 - Math.floor(glob.startDate % 86400 % 3600 %60);

    console.log((_dias1 - _dias)+ ',' + _horas + ',' + _min + ',' + _segs);
    */

    glob.total   = Math.floor((glob.endDate - glob.startDate)/86400);
    glob.days    = Math.floor((glob.endDate - glob.now ) / 86400);
    glob.hours   = 24 - Math.floor(((glob.endDate - glob.now) % 86400) / 3600);
    glob.minutes = 60 - Math.floor((((glob.endDate - glob.now) % 86400) % 3600) / 60) ;
    glob.seconds = 60 - Math.floor((glob.endDate - glob.now) % 86400 % 3600 % 60);
    
    if (glob.now >= glob.endDate) {
        return;
    }
    
    var clock = {
        set: {
            days: function(){
                var cdays = $("#canvas_days").get(0);
                var ctx = cdays.getContext("2d");
                ctx.clearRect(0, 0, cdays.width, cdays.height);
                ctx.beginPath();
                ctx.strokeStyle = glob.daysColor;
                
                ctx.shadowBlur    = 5;
                ctx.shadowOffsetX = 0;
                ctx.shadowOffsetY = 0;
                ctx.shadowColor = glob.daysGlow;
                
                ctx.arc(94,94,85, deg(0), deg((360/glob.total)*(glob.total - glob.days)));
                ctx.lineWidth = 4;
                ctx.stroke();
                $(".clock_days .val").text(glob.days);
            },
            
            hours: function(){
                var cHr = $("#canvas_hours").get(0);
                var ctx = cHr.getContext("2d");
                ctx.clearRect(0, 0, cHr.width, cHr.height);
                ctx.beginPath();
                ctx.strokeStyle = glob.hoursColor;
                
                ctx.shadowBlur    = 5;
                ctx.shadowOffsetX = 0;
                ctx.shadowOffsetY = 0;
                ctx.shadowColor = glob.hoursGlow;
                
                ctx.arc(94,94,85, deg(0), deg(15*glob.hours));
                ctx.lineWidth = 4;
                ctx.stroke();
                $(".clock_hours .val").text(24 - glob.hours);
            },
            
            minutes : function(){
                var cMin = $("#canvas_minutes").get(0);
                var ctx = cMin.getContext("2d");
                ctx.clearRect(0, 0, cMin.width, cMin.height);
                ctx.beginPath();
                ctx.strokeStyle = glob.minutesColor;
                
                ctx.shadowBlur    = 5;
                ctx.shadowOffsetX = 0;
                ctx.shadowOffsetY = 0;
                ctx.shadowColor = glob.minutesGlow;
                
                ctx.arc(94,94,85, deg(0), deg(6*glob.minutes));
                ctx.lineWidth = 4;
                ctx.stroke();
                $(".clock_minutes .val").text(60 - glob.minutes);
            },
            seconds: function(){
                var cSec = $("#canvas_seconds").get(0);
                var ctx = cSec.getContext("2d");
                ctx.clearRect(0, 0, cSec.width, cSec.height);
                ctx.beginPath();
                ctx.strokeStyle = glob.secondsColor;
                
                ctx.shadowBlur    = 5;
                ctx.shadowOffsetX = 0;
                ctx.shadowOffsetY = 0;
                ctx.shadowColor = glob.secondsGlow;
                
                ctx.arc(94,94,85, deg(0), deg(6*glob.seconds));
                ctx.lineWidth = 4;
                ctx.stroke();
        
                $(".clock_seconds .val").text(60 - glob.seconds);
            }
        },
       
        start: function(){
            /* Seconds */
            var cdown = setInterval(function(){
                if ( glob.seconds > 59 ) {
                    if (60 - glob.minutes == 0 && 24 - glob.hours == 0 && glob.days == 0) {
                        clearInterval(cdown);
                        /* Countdown is complete */
                        return;
                    }
                    glob.seconds = 1;
                    if (glob.minutes > 59) {
                        glob.minutes = 1;
                        clock.set.minutes();
                        if (glob.hours > 23) {
                            glob.hours = 1;
                            if (glob.days > 0) {
                                glob.days--;
                                clock.set.days();
                            }
                        } else {
                            glob.hours++;
                        }
                        clock.set.hours();
                    } else {
                        glob.minutes++;
                    }
                    clock.set.minutes();
                } else {
                    glob.seconds++;
                }
                clock.set.seconds();
            },1000);
        }
    }
    clock.set.seconds();
    clock.set.minutes();
    clock.set.hours();
    clock.set.days();
    clock.start();
}
/*
 Example

$(document).ready(function(){
    JBCountDown({
            secondsColor : "#fdf794",
            secondsGlow : "#fdf794",
            minutesColor : "#00be70",
            minutesGlow : "#00be70",
            hoursColor : "#0073c6",
            hoursGlow : "#0073c6",
            daysColor : "#ff7676",
            daysGlow : "#ff7676",
            startDate : "0", //seg
            endDate : "60019", // 172800 seg = 2 dias
            now : "0", //seg
    });
}); 
 **/




/*
 *Example HTML
 *
 *<div class="wrapper_count">
            <img class="logo_life" src="https://d24qcl29idsgko.cloudfront.net/img/logo.png" alt="Logo LIFE!" />
            <br/>
            <img class="count_life" src="https://d24qcl29idsgko.cloudfront.net/img/contador.png" alt="Contador LIFE!" />
            <br/>
            <br/>
            <h1>Miles y millones de jóvenes unidos, un mundo conectado bajo una nueva fórmula, LIFE!</h1>
            <br/>
            <!-- Seconds -->
            
            <div class="clock_days">
                <div class="bgLayer">
                    <div class="topLayer"></div>
                    <canvas id="canvas_days" width="188" height="188">
                    </canvas>
                    <div class="text">
                        <p class="val">19</p>
                        <p class="type_days">Dias</p>
                    </div>
                </div>
            </div>
            <div class="clock_hours">
                <div class="bgLayer">
                    <div class="topLayer"></div>
                    <canvas id="canvas_hours" width="188" height="188">
                    </canvas>
                    <div class="text">
                        <p class="val">7</p>
                        <p class="type_hours">Horas</p>
                    </div>
                </div>
            </div>
            <div class="clock_minutes">
                <div class="bgLayer">
                    <div class="topLayer"></div>
                    <canvas id="canvas_minutes" width="188" height="188">
                    </canvas>
                    <div class="text">
                        <p class="val">19</p>
                        <p class="type_minutes">Minutos</p>
                    </div>
                </div>
            </div>
            <div class="clock_seconds">
                <div class="bgLayer">
                    <div class="topLayer"></div>
                    <canvas id="canvas_seconds" width="188" height="188">
                    </canvas>
                    <div class="text">
                        <p class="val">14</p>
                        <p class="type_seconds">Segundos</p>
                    </div>
                </div>
            </div>
            <!-- Days -->
            <br style="clear:both" />
        </div>
 **/