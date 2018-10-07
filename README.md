# Docker Registry UI
A tiny docker registry ui by PHP, it's useful to list registries/tags/layers/blobs in your registry.
[![DockerHub Badge](http://dockeri.co/image/zhangsean/registry-ui)](https://hub.docker.com/r/zhangsean/registry-ui/)

### Usage
To start a web ui for your registry `10.0.1.100:5000`:
```
docker run -itd -p 5080:80 -e REGISTRY_API=http://10.0.1.100:5000/v2 -e REGISTRY_WEB=10.0.1.100:5000 zhangsean/registry-ui
```

If you visit registry with a uri `hub.local.com`, you may start the web ui using following command:
```
docker run -itd -p 5080:80 -e REGISTRY_API=http://10.0.1.100:5000/v2 -e REGISTRY_WEB=hub.local.com zhangsean/registry-ui
```

To enable the feature showing image total size in home page, which may cause homepage loading slowly, you may start the registry UI using this environment variables `-e SHOW_IMAGE_SIZE=true`, e.g.
```
docker run -itd -p 5080:80 -e REGISTRY_API=http://10.0.1.100:5000/v2 -e REGISTRY_WEB=hub.local.com -e SHOW_IMAGE_SIZE=true zhangsean/registry-ui
```
Visit http://server-ip:5080/ for your registry ui.
