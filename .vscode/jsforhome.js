
      document.addEventListener("DOMContentLoaded", function () {
          const scrollContainer = document.querySelector(".announcement-scroll-container");
           
          let isDown = false;
          let startX;
          let scrollLeft;
      
          scrollContainer.addEventListener("mousedown", (e) => {
              isDown = true;
              scrollContainer.classList.add("grabbing");
              startX = e.pageX - scrollContainer.offsetLeft;
              scrollLeft = scrollContainer.scrollLeft;
          });
      
          scrollContainer.addEventListener("mouseleave", () => {
              isDown = false;
              scrollContainer.classList.remove("grabbing");
          });
      
          scrollContainer.addEventListener("mouseup", () => {
              isDown = false;
              scrollContainer.classList.remove("grabbing");
          });
      
          scrollContainer.addEventListener("mousemove", (e) => {
              if (!isDown) return;
              e.preventDefault();
              const x = e.pageX - scrollContainer.offsetLeft;
              const walk = (x - startX) * 2; // Adjust scroll speed
              scrollContainer.scrollLeft = scrollLeft - walk;
          });
      });
     