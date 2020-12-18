<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<!DOCTYPE html>
<html>
<head>
<body>
<canvas id="canvas" width="600" height="800" tabindex="1"></canvas>
<body>
<script>

// Get a reference to the canvas DOM element
var canvas = document.getElementById("canvas");
// Get the canvas drawing context
var context = canvas.getContext("2d");

var gameEnded = false;

var score = 0; //player score

var positions = [50, 250, 450];
var getPosition = 0;

// Properties for your car
var x = 300; // X position
var y = 650; // Y position
var movement = 50;
var sideLength = 100; // Length of each side of the square

// FLags to track which keys are pressed
var right = false;
var left = false;
var center = false;

// Properties for the target square
var targetX = 0;
var targetY = 0;
var speed = 1;
var targetLength = 75;

//propertires for road lines
var lineLength = 25;
var lineWidth = 5;

//propertires for barriers
var barriersX = 0;
var barriersXT = 0;
var barriersLength = 100;
var barriersWidth = 25;

// Determine if number a is within the range b to c (exclusive)
function isWithin(a, b, c) {
  return (a > b && a < c);
}

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
  if (event.keyCode === 83) { // center
    center = true;
  }
  if (event.keyCode === 40) { // center
    center = true;
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
  if (event.keyCode === 83) { // center
    center = false;
  }
  if (event.keyCode === 40) { // center
    center = false;
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
  context.font = '18px Arial';
  context.fillText('Press Left, Right, and Down or A, S and D to move!', canvas.width / 2, (canvas.height / 4) * 3);
  // Start the game on a click
  canvas.addEventListener('click', startGame);
}
// Start the game
function startGame() {
  // Stop listening for click events
  canvas.removeEventListener('click', startGame);
  // Put the target at a random starting point
  setTargetPosition();
  moveBarriers();
  // Kick off the draw loop
  draw();
}

// Show the game over screen
function endGame() {
  // Display the final score
  erase();
  context.fillStyle = '#000000';
  context.font = '24px Arial';
  context.textAlign = 'center';
  context.fillText('Game Ended', canvas.width / 2, canvas.height / 4);
  context.fillText('Score: ' + score, canvas.width / 2, canvas.height / 2);
}

function setTargetPosition() {
  getPosition = Math.round(Math.random());
  targetX = positions[getPosition];
}

// Sets target to beginning
function moveBarriers() {
   if(targetX === 50) {
     barriersX = 245;
     barriersXT = 425;
   }
   else if(targetX === 250) {
     barriersX = 45;
     barriersXT = 425;
   }
   else {
     barriersX = 425;
     barriersXT = 45;
   }
}

// Clear the canvas
function erase() {
  context.fillStyle = '#FFFFFF';
  context.fillRect(0, 0, 600, 800);
}

function sendScore(scored) {
$.post("saveScore.php",
  {
    scores:scored
  },
  function(data){
    console.log("Data: " + data);
  });
}

// The main draw loop
function draw() {
  erase();
  // Move the square on the grid
  if (right) {
    x = positions[2];
  }
  if (left) {
    x = positions[0];
  }
  if (center) {
    x = positions[1];
  }
  // Collide with the target
  if (isWithin(barriersX, x, x + sideLength) || isWithin(barriersX + barriersLength, x, x + sideLength)) {
    if (isWithin(targetY, y, y + sideLength) || isWithin(targetY + barriersWidth, y, y + sideLength)) { // Y
      gameEnded = true;
  }
 }
  if (isWithin(barriersXT, x, x + sideLength) || isWithin(barriersXT + barriersLength, x, x + sideLength)) {
    if (isWithin(targetY, y, y + sideLength) || isWithin(targetY + barriersWidth, y, y + sideLength)) { // Y
      gameEnded = true;
  }
 }
  if (isWithin(targetX, x, x + sideLength) || isWithin(targetX + targetLength, x, x + sideLength)) {
    if (isWithin(targetY, y, y + sideLength) || isWithin(targetY + targetLength, y, y + sideLength)) { // Y
        // Respawn the target
        setTargetPosition();
        moveBarriers();
        // Increase the score
        score += 1;
        // Increase the Speed
        speed += 0.5;
        
        targetY = 0;
  }
 }
  // Draw the square
  context.fillStyle = '#000000';
  context.fillRect(x, y, sideLength, sideLength);
  // Draw the target 
  context.fillStyle = '#00FF00';
  context.fillRect(targetX, targetY, targetLength, targetLength);
  // Draw the lines 
  context.fillStyle = '#FFFF00';
  context.fillRect(175, targetY, lineWidth, lineLength);
  context.fillStyle = '#FFFF00';
  context.fillRect(375, targetY, lineWidth, lineLength);
  // Draw the barriers 
  context.fillStyle = '#FFA500';
  context.fillRect(barriersX, targetY, barriersLength, barriersWidth);
  context.fillStyle = '#FFA500';
  context.fillRect(barriersXT, targetY, barriersLength, barriersWidth);
  // Draw the score and time remaining
  context.fillStyle = '#000000';
  context.font = '24px Arial';
  context.textAlign = 'left';
  context.fillText('Score: ' + score, 10, 24);
  //move everything
  targetY += speed;
  if (!gameEnded) {
  window.requestAnimationFrame(draw);
  }
  else {
    sendScore(score);
    endGame();
  }
}

//starts game.
menu();
canvas.focus();
</script>
</head>
</html>
