pipeline {
    agent any

    environment {
        PHP_PATH = 'C:\\xampp\\php\\php.exe'
        PROJECT_PATH = 'C:\\Program Files\\folder-xampp\\htdocs\\PetVerse'
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('PHP Lint') {
            steps {
                bat "${PHP_PATH} -l ${PROJECT_PATH}\\*.php"
            }
        }

        stage('Unit Tests') {
            steps {
                dir(PROJECT_PATH) {
                    bat "${PHP_PATH} phpunit.phar --log-junit test-reports/junit.xml"
                }
            }
        }

        stage('Code Coverage') {
            steps {
                dir(PROJECT_PATH) {
                    bat "${PHP_PATH} phpunit.phar --coverage-html coverage-report"
                }
            }
        }

        stage('Deploy to Test') {
            when { branch 'develop' }
            steps {
                bat "xcopy /E /I /Y ${PROJECT_PATH} C:\\xampp\\htdocs\\PetVerse-Test"
            }
        }
    }

    post {
        always {
            junit 'test-reports/junit.xml'
            publishHTML([
                allowMissing: false,
                alwaysLinkToLastBuild: true,
                keepAll: true,
                reportDir: 'coverage-report',
                reportFiles: 'index.html',
                reportName: 'Coverage Report'
            ])
        }
    }
}