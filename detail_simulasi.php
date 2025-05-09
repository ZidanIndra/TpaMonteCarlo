                <thead>
                    <tr>
                        <th>No Truk</th>
                        <th>Waktu Kedatangan</th>
                        <th>Waktu Mulai Pelayanan</th>
                        <th>Waktu Selesai Pelayanan</th>
                        <th>Waktu Tunggu</th>
                        <th>Waktu Pelayanan</th>
                        <th>Waktu di Sistem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Sort the simulation data by truck number
                    ksort($detail_simulation);
                    foreach ($detail_simulation as $no_truk => $data): 
                    ?>
                    <tr>
                        <td><?php echo $no_truk; ?></td>
                        <td><?php echo $data['waktu_kedatangan']; ?></td>
                        <td><?php echo $data['waktu_mulai_pelayanan']; ?></td>
                        <td><?php echo $data['waktu_selesai_pelayanan']; ?></td>
                        <td><?php echo $data['waktu_tunggu']; ?></td>
                        <td><?php echo $data['waktu_pelayanan']; ?></td>
                        <td><?php echo $data['waktu_di_sistem']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody> 