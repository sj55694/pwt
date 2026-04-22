console.debug("Hello world!");

let map = L.map('map').setView([53.430127, 14.564802], 18);
// L.tileLayer.provider('OpenStreetMap.DE').addTo(map);
L.tileLayer.provider('Esri.WorldImagery').addTo(map);
let marker = L.marker([53.430127, 14.564802]).addTo(map);
marker.bindPopup("<strong>Hello!</strong><br>This is a popup.");

document.getElementById("saveButton").addEventListener("click", function() {
    leafletImage(map, function (err, canvas) {
        // here we have the canvas
        let rasterMap = document.getElementById("rasterMap");
        let rasterContext = rasterMap.getContext("2d");

        rasterContext.drawImage(canvas, 0, 0, 300, 150);

        // Create 4x4 puzzle grid
        createPuzzleGrid(rasterMap);
        setupPuzzleStartDropZone();
    });
});

function createPuzzleGrid(sourceCanvas) {
    const puzzleContainer = document.querySelector(".puzzle_start");
    puzzleContainer.innerHTML = ""; // Clear existing pieces

    const gridRows = 4;
    const gridCols = 4;
    const pieceWidth = sourceCanvas.width / gridCols;
    const pieceHeight = sourceCanvas.height / gridRows;

    // Create all pieces first
    const pieces = [];
    for (let row = 0; row < gridRows; row++) {
        for (let col = 0; col < gridCols; col++) {
            const canvas = document.createElement("canvas");
            canvas.width = pieceWidth;
            canvas.height = pieceHeight;
            canvas.className = "puzzle_piece";
            canvas.draggable = true;
            canvas.dataset.row = row;
            canvas.dataset.col = col;

            // Store piece number as data attribute (not visible to user)
            const pieceNumber = row * gridCols + col + 1;
            canvas.dataset.pieceNumber = pieceNumber;

            const ctx = canvas.getContext("2d");
            ctx.drawImage(
                sourceCanvas,
                col * pieceWidth,
                row * pieceHeight,
                pieceWidth,
                pieceHeight,
                0,
                0,
                pieceWidth,
                pieceHeight
            );

            // Drag event listeners
            canvas.addEventListener("dragstart", function(e) {
                this.style.opacity = "0.5";
                e.dataTransfer.effectAllowed = "move";
                e.dataTransfer.setData("text/html", this.innerHTML);
            });

            canvas.addEventListener("dragend", function(e) {
                this.style.opacity = "1";
            });

            pieces.push(canvas);
        }
    }

    // Shuffle pieces randomly using Fisher-Yates algorithm
    for (let i = pieces.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [pieces[i], pieces[j]] = [pieces[j], pieces[i]];
    }

    // Append shuffled pieces to container
    pieces.forEach(piece => {
        puzzleContainer.appendChild(piece);
    });

    // Add drop target listeners to puzzle_stop
    const puzzleStop = document.querySelector(".puzzle_stop");

    puzzleStop.addEventListener("dragover", function(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = "move";
        this.classList.add("drag-over");
    });

    puzzleStop.addEventListener("dragleave", function(e) {
        this.classList.remove("drag-over");
    });

    puzzleStop.addEventListener("drop", function(e) {
        e.preventDefault();
        this.classList.remove("drag-over");

        const draggedPiece = document.querySelector(".puzzle_piece[style*='opacity: 0.5']");
        if (draggedPiece) {
            this.appendChild(draggedPiece);
            // Check if puzzle is now complete
            checkPuzzleCompletion();
        }
    });
}

// Add drop target listeners to puzzle_start to allow moving pieces back
function setupPuzzleStartDropZone() {
    const puzzleStart = document.querySelector(".puzzle_start");

    puzzleStart.addEventListener("dragover", function(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = "move";
        this.classList.add("drag-over");
    });

    puzzleStart.addEventListener("dragleave", function(e) {
        this.classList.remove("drag-over");
    });

    puzzleStart.addEventListener("drop", function(e) {
        e.preventDefault();
        this.classList.remove("drag-over");

        const draggedPiece = document.querySelector(".puzzle_piece[style*='opacity: 0.5']");
        if (draggedPiece) {
            this.appendChild(draggedPiece);
        }
    });
}

// Check if puzzle is completed in correct order
function checkPuzzleCompletion() {
    const puzzleStop = document.querySelector(".puzzle_stop");
    const pieces = puzzleStop.querySelectorAll(".puzzle_piece");

    // Need all 16 pieces
    if (pieces.length !== 16) {
        return false;
    }

    // Check if they are in correct order
    for (let i = 0; i < pieces.length; i++) {
        const expectedNumber = i + 1;
        const actualNumber = parseInt(pieces[i].dataset.pieceNumber);
        if (actualNumber !== expectedNumber) {
            return false;
        }
    }

    // Puzzle is complete and in correct order!
    puzzleStop.classList.add("completed");
    puzzleStop.innerHTML = "GRATULACJE";

    // Show notification using Notification API
    if ('Notification' in window) {
        if (Notification.permission === 'granted') {
            new Notification('Congrats!', {
                body: 'You have successfully completed the puzzle! 🎉',
                icon: 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><text y="75" font-size="75">🎉</text></svg>'
            });
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    new Notification('Congrats!', {
                        body: 'You have successfully completed the puzzle! 🎉',
                        icon: 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><text y="75" font-size="75">🎉</text></svg>'
                    });
                }
            });
        }
    }

    return true;
}

document.getElementById("getLocation").addEventListener("click", function(event) {
    if (! navigator.geolocation) {
        console.log("No geolocation.");
    }

    navigator.geolocation.getCurrentPosition(position => {
        console.log(position);
        let lat = position.coords.latitude;
        let lon = position.coords.longitude;

        map.setView([lat, lon]);
    }, positionError => {
        console.error(positionError);
    });
});

