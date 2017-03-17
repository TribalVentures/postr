#!/bin/bash
SRC_REPO="git@github.com:TribalVentures/postr.git"
AWS_RELEASE_BUCKET="com.interiorpostr.dev/releases/"
LAST_TAG=`git describe --abbrev=0 --tags`

if [[ ! -z "$1" ]]; then
    RELEASE_TAG=$1
else
    echo "You must specify a release tag!"
    git ls-remote --tags | cut -d '/' -f3 
    exit 1
fi

git tag "$1" && git push --tags origin master
if [ "$?" -ne 0 ];then
	exit 1
fi

if [[ $RELEASE_TAG == v[[:digit:]]* ]]; then
    RELEASE_VER=${RELEASE_TAG:1}
else
    RELEASE_VER=$RELEASE_TAG
fi

RELEASE_BASE="postr"
RELEASE_DIR="${RELEASE_BASE}-${RELEASE_VER}"
RELEASE_ZIP="${RELEASE_BASE}-${RELEASE_VER}.tar.gz"
RELEASE_TAG="v${RELEASE_VER}"
RM_DIST=0
STARTING_PATH=`pwd`

function quit {
    cd ${STARTING_PATH}
    if [[ $RM_DIST == 1 ]]; then
        rm -rf dist
    fi
    exit $1
}

if [ ! -d dist ]; then
    mkdir dist
    RM_DIST=1
fi

echo "Create distribution ${RELEASE_ZIP}"
cd dist && git clone "${SRC_REPO}" "${RELEASE_DIR}" 

if [ ! -d "${RELEASE_DIR}" ]; then
    quit 1
fi
    
 cd "${RELEASE_DIR}" && git reset --hard "${RELEASE_TAG}" && rm -rf .git* &&
    cd .. && tar -czf "${RELEASE_ZIP}" "${RELEASE_DIR}/" && rm -rf "${RELEASE_DIR}" && cd ..

if [ ! -f "dist/${RELEASE_ZIP}" ]; then
   quit 1
fi

echo "Collecting commits from ${LAST_TAG} to ${RELEASE_TAG}.."
COMMITS=`git log ${LAST_TAG}..${RELEASE_TAG} --format=" * %s (%h) (%aN)" | grep -v  '* Merge'`
DBPATCH=""
RELEASE_NOTES=""
#DBPATCH=`git diff --name-only ${LAST_TAG}..${RELEASE_TAG} db/patch.sql`
#NOTES=`git diff --name-only ${LAST_TAG}..${RELEASE_TAG} notes.md`


##read -r -d '' RELEASE_STEPS_STAGING << EOM
### from the postr repo
##git checkout ${RELEASE_TAG}
##mysql -h stagingdb -u RealEstate_admin -p RealEstate < db/patch.sql
##ssh ubuntu@staging sudo -u dart /opt/dart/bin/install.sh ${RELEASE_BASE} ${RELEASE_VER}
##EOM
##
##read -r -d '' RELEASE_STEPS_PRODUCTION << EOM
### from the homedart repo
##git checkout ${RELEASE_TAG}
##mysql -h productiondb -u RealEstate_admin -p RealEstate < db/patch.sql
##ssh ubuntu@production sudo -u dart /opt/dart/bin/install.sh ${RELEASE_BASE} ${RELEASE_VER}
##EOM

##if [[ ! -z $NOTES ]]; then
##    RELEASE_NOTES=`cat notes.md`
##fi

if [[ -z "$DBPATCH" ]]; then
    RELEASE_STEPS_STAGING="ssh interiorpostr-test sudo -u tribal /opt/tribal/bin/install.sh ${RELEASE_VER}"
    RELEASE_STEPS_PRODUCTION="ssh interiorpostr sudo -u tribal /opt/tribal/bin/install.sh ${RELEASE_VER}"
fi

echo "Distribution created, upload to s3."
aws s3 cp "dist/${RELEASE_ZIP}" "s3://${AWS_RELEASE_BUCKET}" 
echo
echo "Release ${RELEASE_TAG} has been published."
echo

read -r -d '' RELEASE_NOTES << EOM
# Features & Fixes
$COMMITS

# Install steps
${RELEASE_NOTES}
### Staging
\`\`\`
${RELEASE_STEPS_STAGING}
\`\`\`

### Production
\`\`\`
${RELEASE_STEPS_PRODUCTION}
\`\`\`
EOM

printf "${RELEASE_NOTES}\n"

quit 0

