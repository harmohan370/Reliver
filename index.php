<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8" />
    <link rel="icon" href="./favicon.png" />
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@1.*/css/pico.min.css">
    <meta name="viewport" content="width=device-width" />
</head>
<body data-sveltekit-preload-data="hover">
    <div style="display: contents">
        <div><center><h3>attendance Done, Go check <a href="/prereliver">prereliver</a> list</h3></center></div>
        <table role="grid">
            <thead>
                <tr>
                    <th scope="col">S. No.</th>
                    <th scope="col">Name</th>
                    <th scope="col">Father&#39;s Name</th>
                    <th scope="col">Contact</th>
                    <th scope="col">Email</th>
                    <th scope="col">NEIS No.</th>
                    <th scope="col">attendance</th>
                    <th scope="col">Past 5 days</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <?php
            // You can include your PHP code here if needed
        ?>

        <script>
            {
                __sveltekit_110fc8n = {
                    env: {},
                    base: new URL(".", location).pathname.slice(0, -1),
                    element: document.currentScript.parentElement
                };

                const data = [null, null];

                Promise.all([
                    import("./_app/immutable/entry/start.ff2e653b.js"),
                    import("./_app/immutable/entry/app.3f5b3d4b.js")
                ]).then(([kit, app]) => {
                    kit.start(app, __sveltekit_110fc8n.element, {
                        node_ids: [0, 2],
                        data,
                        form: null,
                        error: null
                    });
                });
            }
        </script>
    </div>
</body>
</html>
