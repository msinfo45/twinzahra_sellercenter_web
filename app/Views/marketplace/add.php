<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="content-wrapper">

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        Marketplace
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Marketplace</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <div class="content">

   <div id="ResultData"></div>


    </div>

</div>
</div>




<script>

    //Funcion Pending Order
    function loadData(){

        var displayProduct = 5;
        $('#ResultData').html(createSkeleton(displayProduct));

        setTimeout(function(){
            loadData(displayProduct);
        }, 0);

        function createSkeleton(limit){
            var skeletonHTML = '';
            for(var i = 0; i < limit; i++){
                skeletonHTML += '<div class="ph-item">';
                skeletonHTML += '<div class="ph-col-4">';
                skeletonHTML += '<div class="ph-picture"></div>';
                skeletonHTML += '</div>';
                skeletonHTML += '<div>';
                skeletonHTML += '<div class="ph-row">';
                skeletonHTML += '<div class="ph-col-12 big"></div>';
                skeletonHTML += '<div class="ph-col-12"></div>';
                skeletonHTML += '<div class="ph-col-12"></div>';
                skeletonHTML += '<div class="ph-col-12"></div>';
                skeletonHTML += '<div class="ph-col-12"></div>';
                skeletonHTML += '</div>';
                skeletonHTML += '</div>';
                skeletonHTML += '</div>';
            }
            return skeletonHTML;
        }

        function loadData(limit){
            $.ajax({
                url:'<?= base_url('marketplace/load_marketplace') ?>',
                method:"POST",
                data:{action: 'load_marketplace', limit:limit},
                success:function(data) {
                    $('#ResultData').html(data);
                }
            });
        }
    }


    //Default load
    loadData();
    //




</script>




<?= $this->endSection() ?>

