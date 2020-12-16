<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<!DOCTYPE html>
<html>
<head>
<body>
<canvas id="canvas" width="600" height="800" tabindex="1"></canvas>
<body>
<script>
// Get a reference to the canvas DOM element
var canvas = document.getElementById('canvas');
// Get the canvas drawing context
var context = canvas.getContext('2d');

var score = 0; //player score

// Properties for your car
var x = 300; // X position
var y = 25; // Y position
var movement = 200;
var sideLength = 100; // Length of each side of the square

// FLags to track which keys are pressed
var right = false;
var left = false;

// Properties for the target square
var targetX = 0;
var targetY = 0;
var speed = 0;
var targetLength = 75;

//propertires for road lines
var lineX = 0;
var lineY = 0;
var lineLength = 25;
var lineWidth = 5;

//propertires for barriers
var barriersX = 0;
var barriersY = 0;
var barriersLength = 25;
var barriersWidth = 5;

// Determine if number a is within the range b to c (exclusive)
function isWithin(a, b, c) {
  return (a > b && a < c);
}

// Countdown timer (in seconds) for time inbetween walls
var countdown = 2;
// ID to track the setTimeout
var id = null;

// Listen for keydown events
canvas.addEventListener('keydown', function(event) {
  event.preventDefault();
  console.log(event.key, event.keyCode);
  if (event.keyCode === 37) { // LEFT
    left = true;
  }
  if (event.keyCode === 39) { // RIGHT
    right = true;
  }
    if (event.keyCode === 65) { // LEFT
    left = true;
  }
  if (event.keyCode === 68) { // RIGHT
    right = true;
  }
});

// Listen for keyup events
canvas.addEventListener('keyup', function(event) {
  event.preventDefault();
  console.log(event.key, event.keyCode);
  if (event.keyCode === 37) { // LEFT
    left = false;
  }
  if (event.keyCode === 39) { // RIGHT
    right = false;
  }
    if (event.keyCode === 65) { // LEFT
    left = false;
  }
  if (event.keyCode === 68) { // RIGHT
    right = false;
  }
});
//shows beginning menu
function menu() {
  erase();
  context.fillStyle = '#000000';
  context.font = '36px Arial';
  context.textAlign = 'center';
  context.fillText('Welcome to Car Game!', canvas.width / 2, canvas.height / 4);
  context.font = '24px Arial';
  context.fillText('Click to Start', canvas.width / 2, canvas.height / 2);
  context.font = '18px Arial'
  context.fillText('Press Left and Right or A and D to move!', canvas.width / 2, (canvas.height / 4) * 3);
  // Start the game on a click
  canvas.addEventListener('click', startGame);
}
// Start the game
function startGame() {
	// Reduce the countdown timer ever second
  id = setInterval(function() {
    countdown--;
  }, 1000)
  // Stop listening for click events
  canvas.removeEventListener('click', startGame);
  // Put the target at a random starting point
	moveTarget();
  // Kick off the draw loop
  draw();
}

// Show the game over screen
function endGame() {
	// Stop the countdown
  clearInterval(id);
  // Display the final score
  erase();
  context.fillStyle = '#000000';
  context.font = '24px Arial';
  context.textAlign = 'center';
}

// Sets target to beginning
function moveTarget() {
  targetX = Math.round(Math.random() * canvas.width - targetLength);
  targetY = Math.round(Math.random() * canvas.height - targetLength)
}

// Clear the canvas
function erase() {
  context.fillStyle = '#FFFFFF';
  context.fillRect(0, 0, 600, 400);
}

// The main draw loop
function draw() {
  erase();
  // Move the square on the grid
  if (right && x < 500) {
    x += movement;
  }
  if (left && x > 100) {
    x -= movement;
  }
  // Collide with the target
  if (isWithin(targetX, x, x + sideLength) || isWithin(targetX + targetLength, x, x + sideLength)) { // X
    if (isWithin(targetY, y, y + sideLength) || isWithin(targetY + targetLength, y, y + sideLength)) { // Y
      // Respawn the target
      moveTarget();
      // Increase the score
      score++;
      // Increase the Speed
      speed += .5;
    }
  }
  // Draw the square
  context.fillStyle = '#525432';
  context.fillRect(x, y, sideLength, sideLength);
  // Draw the target 
  context.fillStyle = '#525432';
  context.fillRect(targetX, targetY, targetLength, targetLength);
  // Draw the lines 
  context.fillStyle = '#525432';
  context.fillRect(targetX, targetY, targetLength, targetLength);
  // Draw the barriers 
  context.fillStyle = '#525432';
  context.fillRect(targetX, targetY, targetLength, targetLength);
  // Draw the score and time remaining
  context.fillStyle = '#000000';
  context.font = '24px Arial';
  context.textAlign = 'left';
  context.fillText('Score: ' + score, 10, 24);
  window.requestAnimationFrame(draw);
  }
}

//starts game.
menu();
canvas.focus();
</script>
</head>
</html>
