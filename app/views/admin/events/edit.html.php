<h2>Edit <?= $event['eventname'] ?> on <?= $event['event_start'] ?></h2>

<form action="" method="post">
  <label for="geo_latitude">Location latitude</label>
  <input id="geo_latitude" name="latitude" value="<?=$event['geo']['latitude']?>" />

  <label for="geo_longitude">Location latitude</label>
  <input id="geo_longitude" name="longitude" value="<?=$event['geo']['longitude']?>" />

  <br/><br/>
  <hr/>

  <input type="submit" value="Update" />
  <a href="<?= BASE_URL ?>admin/events?search_event=<?= $event['eventname']?>">Cancel</a>
</form>