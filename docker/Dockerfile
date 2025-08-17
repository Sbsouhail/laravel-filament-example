# Base stage for PHP, Node.js, Composer, and Supervisor
FROM dunglas/frankenphp AS base

WORKDIR /app

ARG USER=appuser
ARG UID=1000
ARG GID=1000
ARG APP_ENV

# Create user and set permissions early for better caching
RUN groupadd -g ${GID} ${USER} && \
    useradd -m -u ${UID} -g ${USER} -s /bin/bash ${USER} && \
    chown -R ${USER}:${USER} /data/caddy /config/caddy && \
    mkdir -p /config/psysh /app/storage/logs && \
    chown -R ${USER}:${USER} /config/psysh /app/storage/logs

# Install PHP extensions and necessary packages
RUN apt-get update && apt-get upgrade -y && apt-get install -y --no-install-recommends \
    supervisor \
    unzip \
    nodejs npm \
    && npm install -g pnpm \
    && apt-get autoremove -y && apt-get autoclean -y && apt-get clean -y \
    && rm -rf /var/lib/apt-get/lists/*

# Install PHP extensions
RUN install-php-extensions \
    exif \
    pdo_pgsql \
    gd \
    intl \
    zip \
    opcache \
    redis

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install global Node.js utilities (only if not in production)
RUN if [ "$APP_ENV" != "production" ]; then \
    npm install -g npm-check-updates; \
    fi

# Set PHP configuration
RUN if [ "$APP_ENV" != "production" ]; then \
    cp $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini ; \
    else \
    cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini ; \
    fi

# Override php.ini
RUN echo "upload_max_filesize=110M" > $PHP_INI_DIR/conf.d/custom.ini && \
    echo "post_max_size=110M" >> $PHP_INI_DIR/conf.d/custom.ini && \
    echo "max_execution_time=300" >> $PHP_INI_DIR/conf.d/custom.ini

# Copy Supervisor configuration
COPY docker/supervisord.conf /etc/supervisor/supervisord.conf

# Final build stage
FROM base AS final

WORKDIR /app

ARG USER=appuser
ARG APP_ENV

# Copy the application source code
COPY . .
RUN chown -R ${USER}:${USER} .

# Switch to user
USER ${USER}


# Optimize Laravel for production
# RUN if [ "$APP_ENV" = "production" ]; then \
#     composer install --no-dev --no-interaction --optimize-autoloader && \
#     composer clear-cache; \
#     fi

# Set environment variables
ENV SERVER_NAME=:80

# Start Supervisor as the main process
CMD ["supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
