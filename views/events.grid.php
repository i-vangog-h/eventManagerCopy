<div class="events-list-container">
    <div class="filter">
        <h1>Events</h1>
        <select id="filterDropdown">
            <option value="any">Any</option>
            <?php foreach(Category::cases() as $category): ?>
                <option value="<?php echo($category->name); ?>"><?php echo($category->name); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="grid">
        <?php foreach ($events as $event): ?>
            <div class="card" 
                data-category="<?php echo( Category::from($event["categoryId"])->name ); ?>" 
            >
                <img src="public/uploads/event.images/<?php echo($event["imageUri"])?>" alt="event image">
                <div class="card-body">
                    <h2 class="card-title">
                        <?php echo(htmlspecialchars($event["name"])); ?>
                    </h2>
                    <p class="card-description">
                        <?php echo(htmlspecialchars($event["description"])); ?> 
                    </p>
                    <ul class="card-list">
                        <li> Location: <?php echo(htmlspecialchars($event["location"])); ?> </li>
                        <li> Start at: <?php echo(htmlspecialchars($event["startAt"])); ?> </li>
                        <li> Entry: 
                            <?php 
                                if ($event["entryPrice"] == 0){
                                    echo("free entry");
                                }
                                else{
                                    echo(htmlspecialchars($event["entryPrice"])."€");
                                }
                            ?> 
                        </li>
                    </ul>
                    <?php if($currentPage == "my.events.php"): ?>
                        <div class="card-delete-action">
                            <form action="my.events.php" method="post">
                                <input type="hidden" name="eventId" value="<?php echo $event['id']; ?>">
                                <button type="submit" name="deleteEvent">
                                    Delete
                                </button>
                            </form>
                        </div>
                    <?php endif?>
                </div>
            </div>
        <?php endforeach ?>
    </div>
    <div class="pagination">
        <?php for ($i = 1; $i <= $pagesCount; $i++): ?>
            <a 
                href="<?php echo($currentPage."?page=".$i); ?>"
                <?php if($pageNumber == $i) echo("class='active'"); ?>
            > 
                <?php echo($i); ?>
            </a>
        <?php endfor; ?>
    </div>
</div>
