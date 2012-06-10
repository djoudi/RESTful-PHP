<form action="" method="post">
  <label for="search_event">Search events</label>
  <input name="search_event" id="search_event" value="<?=isset( $params['search_event'] ) ? $params['search_event'] : ''?>" />
</form>

<? if ( isset( $events ) && ! empty( $events ) ): ?>
  <br/>
  <hr/>

  <table>
    <tr>
      <th>Event name</th>
      <th>Sport</th>
      <th>Start at</th>
      <th>Latitude</th>
      <th>Longitude</th>
      <th>Actions</th>
    </tr>
    <? foreach ( $events as $event ): ?>
    <tr>
      <td><?= $event['eventname'] ?></td>
      <td><?= $event['sport'] ?></td>
      <td><?= $event['event_start'] ?></td>
      <td><?= $event['latitude'] ?></td>
      <td><?= $event['longitude'] ?></td>
      <td><a href="events/<?= $event['id'] ?>/edit">Edit</a></td>
    </tr>
    <? endforeach; ?>
  </table>
<? else: ?>
  No events.
<? endif; ?>