<div class="table-responsive">
    <table id="highscores" class="table table-hover">
        <thead>
            <tr>
                <th>Username</th>
                <th>Level</th>
                <th>Total points</th>
                <th>Typed words</th>
                <th>Misspelled</th>
            </tr>
        </thead>
        <tbody>
        <?php if (isset($user_scores)): ?>
            <tr class="active">
                <td><?= $user_scores["username"] ?></td>
                <td><?= $user_scores["level"] ?></td>
                <td><?= $user_scores["points"] ?></td>
                <td><?= $user_scores["typed"] ?></td>
                <td><?= $user_scores["misspelled"] ?></td>
            </tr>
        <?php endif; ?>
        
    <?php if (empty($scores)): ?>
        </tbody>
    </table>
        <div class="container centered">No one in this list</div>
    <?php else: ?>
            <?php foreach($scores as $row): ?>
                <tr>
                    <td><?= $row["username"] ?></td>
                    <td><?= $row["level"] ?></td>
                    <td><?= $row["points"] ?></td>
                    <td><?= $row["typed"] ?></td>
                    <td><?= $row["misspelled"] ?></td>
                </tr>
            <?php endforeach; ?>
            
        </tbody>
    </table>
    <?php endif; ?>
</div>
