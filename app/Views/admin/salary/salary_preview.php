<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-content">
   <div class="container-fluid">
      <!-- start page title -->
      <div class="row">
         <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
               <ol class="breadcrumb m-0">
                  <li class="breadcrumb-item"><a href="javascript:void(0);">EMS</a></li>
                  <li class="breadcrumb-item active">Salary Slip Preview</li>
               </ol>
               <div class="page-title-right">
                  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                     <a href="<?php echo site_url("salary"); ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Back to List
                     </a>
                     <a href="<?php echo site_url("salary/slip/download/{$salary_id}"); ?>" class="btn btn-primary">
                        <i class="fas fa-download mr-1"></i> Download Slip
                     </a>
                     <button id="print-btn" class="btn btn-info">
                        <i class="fas fa-print mr-1"></i> Print Slip
                     </button>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end page title -->

      <div class="row">
         <div class="col-xl-12">
            <div class="card">
               <?php echo view('admin/_topmessage'); ?>
               <div class="card-body">
                  <div class="pdf-viewer-container">
                     <div class="pdf-toolbar mb-3 d-flex justify-content-between align-items-center">
                        <div class="page-navigation">
                           <button class="btn btn-outline-secondary btn-sm" id="prev-page">
                              <i class="fas fa-chevron-left"></i> Previous
                           </button>
                           <span class="mx-2">Page <span id="page-num">1</span> of <span id="page-count">0</span></span>
                           <button class="btn btn-outline-secondary btn-sm" id="next-page">
                              Next <i class="fas fa-chevron-right"></i>
                           </button>
                        </div>
                        <div class="zoom-controls">
                           <button class="btn btn-outline-secondary btn-sm" id="zoom-out">
                              <i class="fas fa-search-minus"></i>
                           </button>
                           <span class="mx-2"><span id="zoom-level">100</span>%</span>
                           <button class="btn btn-outline-secondary btn-sm" id="zoom-in">
                              <i class="fas fa-search-plus"></i>
                           </button>
                           <button class="btn btn-outline-secondary btn-sm ms-2" id="fit-width">
                              <i class="fas fa-arrows-alt-h"></i> Fit Width
                           </button>
                        </div>
                     </div>

                     <div class="pdf-viewer-wrapper">
                        <canvas id="pdf-render"></canvas>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- Include PDF.js library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
<script>
   // Set the path to the PDF.js worker
   pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

   // PDF rendering variables
   let pdfDoc = null,
      pageNum = 1,
      pageRendering = false,
      pageNumPending = null,
      scale = 1.0,
      canvas = document.getElementById('pdf-render'),
      ctx = canvas.getContext('2d');

   // Get the PDF file
   const url = "<?php echo $pdf_path; ?>";

   /**
    * Get the dimensions of the PDF viewer container
    */
   function getViewerDimensions() {
      const wrapper = document.querySelector('.pdf-viewer-wrapper');
      return {
         width: wrapper.clientWidth,
         height: wrapper.clientHeight
      };
   }

   /**
    * Adjust the scale to fit the page width
    */
   function fitToWidth() {
      const wrapper = document.querySelector('.pdf-viewer-wrapper');
      pdfDoc.getPage(pageNum).then(function(page) {
         const viewport = page.getViewport({
            scale: 1.0
         });
         scale = (wrapper.clientWidth - 20) / viewport.width; // Subtract 20px for padding
         updateZoomLevel();
         renderPage(pageNum);
      });
   }

   /**
    * Render the PDF page
    */
   function renderPage(num) {
      pageRendering = true;

      // Using promise to fetch the page
      pdfDoc.getPage(num).then(function(page) {
         const viewport = page.getViewport({
            scale: scale
         });
         canvas.height = viewport.height;
         canvas.width = viewport.width;

         // Render PDF page into canvas context
         const renderContext = {
            canvasContext: ctx,
            viewport: viewport
         };

         const renderTask = page.render(renderContext);

         // Wait for rendering to finish
         renderTask.promise.then(function() {
            pageRendering = false;
            document.getElementById('page-num').textContent = num;

            if (pageNumPending !== null) {
               // New page rendering is pending
               renderPage(pageNumPending);
               pageNumPending = null;
            }
         });
      });
   }

   /**
    * Go to previous page
    */
   function onPrevPage() {
      if (pageNum <= 1) {
         return;
      }
      pageNum--;
      queueRenderPage(pageNum);
   }
   document.getElementById('prev-page').addEventListener('click', onPrevPage);

   /**
    * Go to next page
    */
   function onNextPage() {
      if (pageNum >= pdfDoc.numPages) {
         return;
      }
      pageNum++;
      queueRenderPage(pageNum);
   }
   document.getElementById('next-page').addEventListener('click', onNextPage);

   /**
    * Zoom in
    */
   function onZoomIn() {
      if (scale >= 3.0) return;
      scale += 0.25;
      updateZoomLevel();
      queueRenderPage(pageNum);
   }
   document.getElementById('zoom-in').addEventListener('click', onZoomIn);

   /**
    * Zoom out
    */
   function onZoomOut() {
      if (scale <= 0.5) return;
      scale -= 0.25;
      updateZoomLevel();
      queueRenderPage(pageNum);
   }
   document.getElementById('zoom-out').addEventListener('click', onZoomOut);

   /**
    * Fit to width
    */
   function onFitWidth() {
      fitToWidth();
   }
   document.getElementById('fit-width').addEventListener('click', onFitWidth);

   /**
    * Update zoom level display
    */
   function updateZoomLevel() {
      document.getElementById('zoom-level').textContent = Math.round(scale * 100);
   }

   /**
    * If another page rendering in progress, waits until the rendering is
    * finished. Otherwise, executes rendering immediately.
    */
   function queueRenderPage(num) {
      if (pageRendering) {
         pageNumPending = num;
      } else {
         renderPage(num);
      }
   }

   /**
    * Print PDF
    */
   document.getElementById('print-btn').addEventListener('click', function() {
      window.open(url, '_blank');
   });

   /**
    * Load the PDF document
    */
   pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
      pdfDoc = pdfDoc_;
      document.getElementById('page-count').textContent = pdfDoc.numPages;

      // Initial/first page rendering - fit to width by default
      fitToWidth();

      // Handle window resize
      window.addEventListener('resize', function() {
         if (pdfDoc) {
            fitToWidth();
         }
      });
   }).catch(function(error) {
      // Display error message
      const errorMessage = document.createElement('div');
      errorMessage.className = 'alert alert-danger';
      errorMessage.textContent = 'Error loading PDF: ' + error.message;
      document.querySelector('.pdf-viewer-wrapper').replaceWith(errorMessage);
   });
</script>

<style>
   .pdf-viewer-container {
      position: relative;
      width: 100%;
      height: 100%;
   }

   .pdf-viewer-wrapper {
      background: #f5f5f5;
      overflow: auto;
      width: 100%;
      height: 75vh;
      min-height: 600px;
      display: flex;
      justify-content: center;
      align-items: center;
      border: 1px solid #dee2e6;
      border-radius: 0.25rem;
      padding: 10px;
   }

   .pdf-toolbar {
      background: #f8f9fa;
      padding: 0.5rem 1rem;
      border-radius: 0.25rem;
      border: 1px solid #dee2e6;
   }

   #pdf-render {
      max-width: 100%;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
   }

   @media (max-width: 768px) {
      .pdf-toolbar {
         flex-direction: column;
         gap: 10px;
      }

      .page-navigation,
      .zoom-controls {
         width: 100%;
         justify-content: center;
      }

      .pdf-viewer-wrapper {
         height: 60vh;
         min-height: 400px;
      }
   }
</style>

<?= $this->endSection() ?>