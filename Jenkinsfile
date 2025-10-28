pipeline {
    agent any

    environment {
        PHP_PATH = 'C:\\xampp\\php\\php.exe'
        PROJECT_PATH = 'C:\\Users\\srhak\\.jenkins\\workspace\\PetVerse'
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('PHP Lint') {
            steps {
                bat "\"${PHP_PATH}\" -l *.php"
            }
        }

        stage('Setup Test Environment') {
            steps {
                // Create directories for test reports
                bat '''
                    if not exist "test-reports" mkdir test-reports
                    if not exist "coverage-report" mkdir coverage-report
                '''
                
                // Download PHPUnit if not exists
                bat '''
                    if not exist "phpunit.phar" (
                        curl -o phpunit.phar https://phar.phpunit.de/phpunit-9.phar
                    )
                '''
            }
        }

        stage('Unit Tests') {
            steps {
                bat "\"${PHP_PATH}\" phpunit.phar --log-junit test-reports\\junit.xml"
            }
        }

        stage('Code Coverage') {
            steps {
                bat "\"${PHP_PATH}\" phpunit.phar --coverage-html coverage-report"
            }
        }

        stage('Deploy to Test') {
            when { branch 'develop' }
            steps {
                bat "xcopy /E /I /Y . C:\\xampp\\htdocs\\PetVerse-Test"
            }
        }
    }

    post {
        always {
            junit allowEmptyResults: true, testResults: 'test-reports/*.xml'
            publishHTML([
                allowMissing: true,
                alwaysLinkToLastBuild: true,
                keepAll: true,
                reportDir: 'coverage-report',
                reportFiles: 'index.html',
                reportName: 'Coverage Report'
            ])
        }
    }
}