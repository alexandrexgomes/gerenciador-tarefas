FROM httpd:2.4

RUN sed -i \
  -e 's/^#LoadModule proxy_module/LoadModule proxy_module/' \
  -e 's/^#LoadModule proxy_fcgi_module/LoadModule proxy_fcgi_module/' \
  -e 's/^#LoadModule rewrite_module/LoadModule rewrite_module/' \
  -e 's/^#LoadModule headers_module/LoadModule headers_module/' \
  /usr/local/apache2/conf/httpd.conf

RUN echo "\nInclude conf/extra/laravel.conf\n" >> /usr/local/apache2/conf/httpd.conf

COPY laravel.conf /usr/local/apache2/conf/extra/laravel.conf