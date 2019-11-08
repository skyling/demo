FROM openresty/openresty

ARG TIME_ZONE=Asia/ShangHai
ENV TIME_ZONE ${TIME_ZONE}
RUN ln -snf /usr/share/zoneinfo/$TIME_ZONE /etc/localtime && echo $TIME_ZONE > /etc/timezone 

COPY ./nginx.conf /usr/local/openresty/nginx/conf/nginx.conf